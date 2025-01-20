<?php

namespace App\Filament\WaliKelas\Resources\ScoresResource\Pages;

use Filament\Actions;
use App\Models\Scores;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ListRecords;
use App\Filament\WaliKelas\Resources\ScoresResource;
use App\Models\classes;
use App\Models\guruMataPelajaran;
use App\Models\schools;
use App\Models\students;
use App\Models\subjects;
use App\Models\teachers;

class ListScores extends ListRecords
{
    protected static string $resource = ScoresResource::class;

    protected static ?string $title = 'Nilai Akhir';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tamba Nilai Akhir'),
            Action::make('import')
                ->label('Import Nilai Akhir')
                ->modalHeading('Import Data dari Excel')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel')
                        ->required()
                        ->directory('excel')
                ])
                ->action(function($data)
                {
                    $file = $data;
            
                    if (!$file) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('File Excel tidak ditemukan.')
                            ->danger()
                            ->send();
                        return;
                    }
            
                    $spreadsheet = IOFactory::load(Storage::path($file['file']));
                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray(null, true, true, true);
            
                    $imported = 0;
            
                    foreach ($rows as $key => $row) {
                        // Abaikan header
                        if ($key === 1) continue;

                        $sekolah = schools::first();

                        $tahunAjaran = $sekolah->academicYear;

                        $semester = $sekolah->semester;

                        $class = classes::where('class_name', $row['B'])->where('academic_year_id', $tahunAjaran->id)->first();

                        $student = students::where('name', $row['A'])->where('class_id', $class->id)->first();

                        $subject = subjects::where('subject_name', $row['C'])->first();

                        $teacher = guruMataPelajaran::where('subject_id', $subject->id)->first()->teacher;
            
                        // Masukkan data ke database
                        Scores::create([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacher->id,
                            'academic_year_id' => $tahunAjaran->id,
                            'semester_id' => $semester->id,
                            'score' => $row['E'],
                            'teacher_notes' => $row['F'],
                        ]);
            
                        $imported++;
                    }
            
                    Notification::make()
                        ->title('Berhasil')
                        ->body("Berhasil mengimpor {$imported} baris data!")
                        ->success()
                        ->send();
                }),
        ];
    }
}
