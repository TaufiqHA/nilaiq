<?php

namespace App\Services\Telegram;

use App\Models\Classes;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

/**
 * Builds Excel files that the Telegram bot can send directly to a chat.
 */
class TelegramExportService
{
    /**
     * @param  array<string, mixed>  $summary
     * @return array{path: string, filename: string}
     */
    public function attendance(Classes $class, array $summary): array
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kehadiran');

        $this->heading($sheet, [
            'No', 'Nama Siswa', 'Hadir', 'Izin', 'Sakit', 'Alfa', 'Tercatat', 'Kehadiran (%)',
        ]);

        $rowNumber = 2;

        foreach ($summary['rows'] as $index => $row) {
            $sheet->fromArray([
                $index + 1,
                $row['student']->name,
                $row['hadir'],
                $row['izin'],
                $row['sakit'],
                $row['alfa'],
                $row['recorded'],
                $row['percentage'],
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->autosize($sheet, 'A', 'H');

        return $this->save(
            $spreadsheet,
            'Rekap_Kehadiran_'.$this->slug($class->name).'_'.$this->dateSuffix().'.xlsx'
        );
    }

    /**
     * @param  array<string, mixed>  $recap
     * @return array{path: string, filename: string}
     */
    public function scores(Classes $class, array $recap): array
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Nilai');

        $this->heading($sheet, [
            'Peringkat', 'Nama Siswa', 'Ulangan Harian', 'Tugas', 'PTS', 'PAS',
            'Jumlah', 'Rata-rata', 'Nilai Akhir', 'Jumlah Nilai',
        ]);

        $rowNumber = 2;

        foreach ($recap['rows'] as $row) {
            $types = $row['type_scores'];

            $sheet->fromArray([
                $row['peringkat'],
                $row['student']->name,
                $types[TelegramIntent::ASSESSMENT_DAILY_TEST]['average'] ?? null,
                $types[TelegramIntent::ASSESSMENT_ASSIGNMENT]['average'] ?? null,
                $types[TelegramIntent::ASSESSMENT_MIDTERM]['average'] ?? null,
                $types[TelegramIntent::ASSESSMENT_FINAL]['average'] ?? null,
                $row['jumlah'],
                $row['rata_rata'],
                $row['final_score'],
                $row['score_count'],
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->autosize($sheet, 'A', 'J');

        return $this->save(
            $spreadsheet,
            'Rekap_Nilai_'.$this->slug($class->name).'_'.$this->dateSuffix().'.xlsx'
        );
    }

    /**
     * @param  array<string, mixed>  $recap
     * @param  array<string, mixed>  $attendance
     * @return array{path: string, filename: string}
     */
    public function full(Classes $class, array $recap, array $attendance): array
    {
        $attendanceRows = collect($attendance['rows'])
            ->keyBy(fn (array $row): int => $row['student']->id);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Lengkap');

        $this->heading($sheet, [
            'Peringkat', 'Nama Siswa', 'Rata-rata', 'Nilai Akhir',
            'Hadir', 'Izin', 'Sakit', 'Alfa', 'Kehadiran (%)',
        ]);

        $rowNumber = 2;

        foreach ($recap['rows'] as $row) {
            $attendanceRow = $attendanceRows->get($row['student']->id);

            $sheet->fromArray([
                $row['peringkat'],
                $row['student']->name,
                $row['rata_rata'],
                $row['final_score'],
                $attendanceRow['hadir'] ?? 0,
                $attendanceRow['izin'] ?? 0,
                $attendanceRow['sakit'] ?? 0,
                $attendanceRow['alfa'] ?? 0,
                $attendanceRow['percentage'] ?? null,
            ], null, 'A'.$rowNumber);

            $rowNumber++;
        }

        $this->autosize($sheet, 'A', 'I');

        return $this->save(
            $spreadsheet,
            'Rekap_Lengkap_'.$this->slug($class->name).'_'.$this->dateSuffix().'.xlsx'
        );
    }

    /**
     * @param  array<int, string>  $headings
     */
    private function heading(Worksheet $sheet, array $headings): void
    {
        $sheet->fromArray($headings, null, 'A1');
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
            ->getFont()
            ->setBold(true);
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')
            ->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()
            ->setARGB('FFE2E8F0');

        $sheet->freezePane('A2');
    }

    private function autosize(Worksheet $sheet, string $start, string $end): void
    {
        foreach (range($start, $end) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * @return array{path: string, filename: string}
     */
    private function save(Spreadsheet $spreadsheet, string $filename): array
    {
        $path = tempnam(sys_get_temp_dir(), 'nilaiq_export_');

        if ($path === false) {
            throw new RuntimeException('Tidak bisa membuat file ekspor sementara.');
        }

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return ['path' => $path, 'filename' => $filename];
    }

    private function slug(string $value): string
    {
        return trim(preg_replace('/[^A-Za-z0-9]+/', '_', $value) ?? '', '_');
    }

    private function dateSuffix(): string
    {
        return now()->format('Ymd_His');
    }
}
