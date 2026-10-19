<?php

namespace App\Services\Telegram;

use App\Models\Classes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Deterministic Indonesian intent parser for the Telegram bot.
 *
 * No external AI service is used: the parser only recognises the intents that
 * the bot can answer with whitelisted database queries.
 */
class RuleBasedTelegramIntentParser
{
    /**
     * @param  Collection<int, Classes>  $classes
     */
    public function parse(string $text, Collection $classes, ?int $contextClassId = null): TelegramIntent
    {
        $normalized = $this->normalize($text);
        $compact = $this->compact($normalized);
        $classes = $classes->values();

        if (preg_match('/^(?:cari|temukan|search)\s+(.+)$/u', $normalized, $matches) === 1) {
            return new TelegramIntent(
                TelegramIntent::SEARCH_STUDENT,
                searchTerm: trim($matches[1]),
            );
        }

        $class = $this->matchClass($normalized, $compact, $classes);
        $matchedClasses = $this->matchAllClasses($normalized, $compact, $classes);
        $student = $this->matchStudent($normalized, $classes, $class?->id);

        $classId = $class?->id;

        if ($classId === null && $student['class_id'] !== null) {
            $classId = $student['class_id'];
        }

        if ($classId === null && $contextClassId !== null) {
            $classId = $contextClassId;
        }

        if ($classId === null && $classes->count() === 1) {
            $classId = $classes->first()->id;
        }

        $studentId = $student['student_id'];
        $limit = $this->extractLimit($normalized, $text);
        $assessment = $this->assessmentType($normalized);
        $period = $this->period($normalized);

        if ($matchedClasses->count() >= 2 && $this->hasAny($normalized, [
            'bandingkan', 'perbandingan', 'dibanding', 'dibandingkan', 'versus', 'vs',
        ])) {
            return new TelegramIntent(
                TelegramIntent::COMPARE_CLASSES,
                classId: $matchedClasses[0]->id,
                secondaryClassId: $matchedClasses[1]->id,
            );
        }

        if ($this->hasAny($normalized, ['ekspor', 'export', 'unduh', 'download'])) {
            if ($this->hasAny($normalized, [
                'kehadiran', 'absen', 'absensi', 'hadir', 'alfa',
            ])) {
                return new TelegramIntent(TelegramIntent::EXPORT_ATTENDANCE, classId: $classId);
            }

            if ($this->hasAny($normalized, ['lengkap', 'semua'])) {
                return new TelegramIntent(TelegramIntent::EXPORT_FULL, classId: $classId);
            }

            return new TelegramIntent(TelegramIntent::EXPORT_SCORES, classId: $classId);
        }

        if ($this->hasAny($normalized, [
            'berisiko', 'perlu perhatian', 'perhatian khusus', 'rawan', 'akan tertinggal', 'kritis',
        ])) {
            return new TelegramIntent(TelegramIntent::AT_RISK, classId: $classId, limit: $limit);
        }

        if ($this->hasAny($normalized, [
            'bawah kkm', 'di bawah kkm', 'belum tuntas', 'tidak tuntas', 'tidak lulus',
        ]) || ($this->hasAny($normalized, ['kkm']) && $this->hasAny($normalized, ['bawah', 'kurang']))) {
            return new TelegramIntent(
                TelegramIntent::BELOW_KKM,
                classId: $classId,
                limit: $limit,
                assessmentType: $assessment,
            );
        }

        if ($this->hasAny($normalized, [
            'belum punya nilai', 'belum ada nilai', 'belum diisi', 'belum dinilai',
            'belum input nilai', 'nilai kosong', 'nilai belum lengkap', 'belum lengkap nilainya',
        ])) {
            return new TelegramIntent(
                TelegramIntent::MISSING_SCORES,
                classId: $classId,
                limit: $limit,
                assessmentType: $assessment,
            );
        }

        $isFullRecap = $this->hasAny($normalized, ['rekap lengkap', 'rekap semua', 'lengkap'])
            && $this->hasAny($normalized, ['rekap', 'ringkasan']);
        $hasAttendance = $this->hasAny($normalized, [
            'kehadiran', 'absen', 'absensi', 'hadir', 'alfa', 'bolos', 'tidak masuk', 'izin', 'sakit',
        ]);
        $hasScore = $this->hasAny($normalized, [
            'nilai', 'rata rata', 'rerata', 'peringkat', 'ranking', 'rangking', 'rekap nilai',
        ]);
        $hasRecap = $this->hasAny($normalized, ['rekap', 'ringkasan']);
        $lowest = $this->hasAny($normalized, [
            'terendah', 'paling rendah', 'paling sedikit', 'paling jarang', 'terkecil', 'paling buruk', 'terbawah',
        ]);
        $highest = $this->hasAny($normalized, [
            'tertinggi', 'paling tinggi', 'paling banyak', 'paling rajin', 'paling sering hadir', 'terbesar', 'terbaik', 'teratas',
        ]);
        $mostAbsent = $this->hasAny($normalized, ['alfa', 'bolos', 'tidak masuk'])
            && ! $this->hasAny($normalized, ['terendah', 'paling rendah', 'paling jarang', 'paling sedikit']);

        if ($this->hasAny($normalized, ['kelas apa', 'daftar kelas', 'kelas saya', 'semua kelas'])) {
            return new TelegramIntent(TelegramIntent::LIST_CLASSES);
        }

        if ($isFullRecap) {
            return new TelegramIntent(
                TelegramIntent::FULL_RECAP,
                classId: $classId,
                studentId: $studentId,
                limit: $limit,
                full: true,
            );
        }

        if ($hasAttendance) {
            if ($studentId !== null && ! $lowest && ! $highest && ! $mostAbsent) {
                return new TelegramIntent(
                    TelegramIntent::STUDENT_ATTENDANCE,
                    classId: $classId,
                    studentId: $studentId,
                );
            }

            if ($mostAbsent) {
                return new TelegramIntent(
                    TelegramIntent::MOST_ABSENT,
                    classId: $classId,
                    limit: $limit,
                    descending: true,
                    period: $period,
                );
            }

            if ($lowest) {
                return new TelegramIntent(
                    TelegramIntent::LOWEST_ATTENDANCE,
                    classId: $classId,
                    limit: $limit,
                    period: $period,
                );
            }

            if ($highest) {
                return new TelegramIntent(
                    TelegramIntent::HIGHEST_ATTENDANCE,
                    classId: $classId,
                    limit: $limit,
                    descending: true,
                    period: $period,
                );
            }

            return new TelegramIntent(
                TelegramIntent::ATTENDANCE_SUMMARY,
                classId: $classId,
                limit: $limit,
                full: $this->hasAny($normalized, ['semua', 'lengkap']),
                period: $period,
            );
        }

        if ($hasScore || $hasRecap) {
            if ($assessment !== null && $studentId !== null) {
                return new TelegramIntent(
                    TelegramIntent::STUDENT_SCORE,
                    classId: $classId,
                    studentId: $studentId,
                    assessmentType: $assessment,
                );
            }

            if ($studentId !== null && ! $lowest && ! $highest) {
                return new TelegramIntent(
                    TelegramIntent::STUDENT_SCORE,
                    classId: $classId,
                    studentId: $studentId,
                    assessmentType: $assessment,
                );
            }

            if ($assessment !== null) {
                return new TelegramIntent(
                    TelegramIntent::ASSESSMENT_RANKING,
                    classId: $classId,
                    limit: $limit,
                    assessmentType: $assessment,
                    descending: ! $lowest,
                );
            }

            if ($lowest) {
                return new TelegramIntent(
                    TelegramIntent::LOWEST_AVERAGE,
                    classId: $classId,
                    limit: $limit,
                );
            }

            if ($highest) {
                return new TelegramIntent(
                    TelegramIntent::HIGHEST_AVERAGE,
                    classId: $classId,
                    limit: $limit,
                    descending: true,
                );
            }

            return new TelegramIntent(
                TelegramIntent::SCORE_RECAP,
                classId: $classId,
                limit: $limit,
                full: $this->hasAny($normalized, ['semua', 'lengkap']),
            );
        }

        return new TelegramIntent(TelegramIntent::HELP, classId: $classId);
    }

    /**
     * Resolve a class from a short command argument such as "7A".
     *
     * @param  Collection<int, Classes>  $classes
     */
    public function findClass(string $text, Collection $classes): ?Classes
    {
        $normalized = $this->normalize($text);

        return $this->matchClass($normalized, $this->compact($normalized), $classes->values());
    }

    /**
     * @param  Collection<int, Classes>  $classes
     */
    private function matchClass(string $normalized, string $compact, Collection $classes): ?Classes
    {
        $matches = [];

        foreach ($classes as $class) {
            $keys = $this->classKeys((string) $class->name);

            foreach ($keys as $key) {
                if ($key === '') {
                    continue;
                }

                $padded = ' '.$normalized.' ';
                if (str_contains($padded, ' '.$key.' ')) {
                    $matches[] = [$key, $class];

                    break;
                }

                $keyCompact = $this->compact($key);
                if ($keyCompact !== '' && str_contains($compact, $keyCompact)) {
                    $matches[] = [$keyCompact, $class];

                    break;
                }
            }
        }

        if ($matches === []) {
            return null;
        }

        usort($matches, fn (array $a, array $b): int => strlen($b[0]) <=> strlen($a[0]));

        return $matches[0][1];
    }

    /**
     * Find every class mentioned in the text.
     *
     * @param  Collection<int, Classes>  $classes
     * @return Collection<int, Classes>
     */
    private function matchAllClasses(string $normalized, string $compact, Collection $classes): Collection
    {
        $matches = [];

        foreach ($classes as $class) {
            foreach ($this->classKeys((string) $class->name) as $key) {
                if ($key === '') {
                    continue;
                }

                $keyCompact = $this->compact($key);
                $keyFound = str_contains(' '.$normalized.' ', ' '.$key.' ')
                    || ($keyCompact !== '' && str_contains($compact, $keyCompact));

                if ($keyFound) {
                    $matches[$class->id] = $class;

                    break;
                }
            }
        }

        return collect(array_values($matches));
    }

    private function period(string $normalized): ?string
    {
        if ($this->hasAny($normalized, ['bulan lalu', 'bulan kemarin'])) {
            return 'last_month';
        }

        if ($this->hasAny($normalized, ['bulan ini'])) {
            return 'this_month';
        }

        return null;
    }

    /**
     * Build the possible normalized keys for a class name.
     *
     * @return array<int, string>
     */
    private function classKeys(string $name): array
    {
        $normalized = $this->normalize($name);
        $keys = [$normalized];

        $parts = explode(' ', $normalized, 2);
        $roman = $this->romanToNumber($parts[0] ?? '');
        if ($roman !== null) {
            $keys[] = trim($roman.' '.($parts[1] ?? ''));
        }

        return array_values(array_unique(array_filter($keys)));
    }

    private function romanToNumber(string $value): ?string
    {
        $map = [
            'xii' => '12', 'xi' => '11', 'ix' => '9', 'x' => '10',
            'viii' => '8', 'vii' => '7', 'vi' => '6', 'iv' => '4',
            'v' => '5', 'iii' => '3', 'ii' => '2', 'i' => '1',
        ];

        return $map[$value] ?? null;
    }

    /**
     * Find a student by full name, or by a unique name fragment.
     *
     * @param  Collection<int, Classes>  $classes
     * @return array{student_id: int|null, class_id: int|null}
     */
    private function matchStudent(string $normalized, Collection $classes, ?int $classId): array
    {
        $candidates = [];

        foreach ($classes as $class) {
            if ($classId !== null && $class->id !== $classId) {
                continue;
            }

            foreach ($class->students as $student) {
                $name = $this->normalize((string) $student->name);
                if ($name === '') {
                    continue;
                }

                $candidates[] = [
                    'student' => $student,
                    'class_id' => $class->id,
                    'name' => $name,
                ];
            }
        }

        $padded = ' '.$normalized.' ';

        // 1. Prefer an exact full-name match.
        $fullMatches = array_values(array_filter(
            $candidates,
            fn (array $item): bool => str_contains($padded, ' '.$item['name'].' '),
        ));

        if (count($fullMatches) === 1) {
            return [
                'student_id' => $fullMatches[0]['student']->id,
                'class_id' => $fullMatches[0]['class_id'],
            ];
        }

        // 2. Fall back to a unique name fragment (first or last name).
        $fragmentMatches = [];

        foreach ($candidates as $item) {
            foreach (explode(' ', $item['name']) as $word) {
                if (mb_strlen($word) < 3) {
                    continue;
                }

                if (str_contains($padded, ' '.$word.' ')) {
                    $fragmentMatches[$item['student']->id] = $item;

                    break;
                }
            }
        }

        if (count($fragmentMatches) === 1) {
            $match = array_values($fragmentMatches)[0];

            return [
                'student_id' => $match['student']->id,
                'class_id' => $match['class_id'],
            ];
        }

        return ['student_id' => null, 'class_id' => null];
    }

    private function assessmentType(string $normalized): ?string
    {
        if ($this->hasAny($normalized, ['pas', 'uas', 'akhir semester'])) {
            return TelegramIntent::ASSESSMENT_FINAL;
        }

        if ($this->hasAny($normalized, ['pts', 'uts', 'tengah semester'])) {
            return TelegramIntent::ASSESSMENT_MIDTERM;
        }

        if ($this->hasAny($normalized, ['tugas'])) {
            return TelegramIntent::ASSESSMENT_ASSIGNMENT;
        }

        if ($this->hasAny($normalized, ['uh', 'ulangan harian', 'ulangan'])) {
            return TelegramIntent::ASSESSMENT_DAILY_TEST;
        }

        return null;
    }

    private function extractLimit(string $normalized, string $original): int
    {
        if (preg_match('/\btop\s+(\d+)\b/i', $original, $matches) === 1) {
            return max(1, min(50, (int) $matches[1]));
        }

        if (preg_match('/\b(\d+)\s+(?:siswa|orang|teratas|terbawah)\b/i', $original, $matches) === 1) {
            return max(1, min(50, (int) $matches[1]));
        }

        if ($this->hasAny($normalized, ['siapa', 'siapakah'])) {
            return 1;
        }

        return 10;
    }

    /**
     * @param  array<int, string>  $phrases
     */
    private function hasAny(string $normalized, array $phrases): bool
    {
        $padded = ' '.$normalized.' ';

        foreach ($phrases as $phrase) {
            $phrase = $this->normalize($phrase);

            if ($phrase === '') {
                continue;
            }

            if (str_contains($phrase, ' ')) {
                if (str_contains($padded, ' '.$phrase.' ')) {
                    return true;
                }

                continue;
            }

            // Accept Indonesian suffix variants such as "kehadirannya" or
            // "nilainya" for longer stems, while keeping short abbreviations
            // ("pas", "uh", "uts") exact to avoid false positives.
            if (mb_strlen($phrase) >= 5) {
                if (str_contains($padded, ' '.$phrase)) {
                    return true;
                }
            } elseif (str_contains($padded, ' '.$phrase.' ')) {
                return true;
            }
        }

        return false;
    }

    private function normalize(string $value): string
    {
        $value = Str::lower($value);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? '';

        return trim(preg_replace('/\s+/', ' ', $value) ?? '');
    }

    private function compact(string $value): string
    {
        return str_replace(' ', '', $value);
    }
}
