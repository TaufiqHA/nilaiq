<?php

namespace App\Http\Controllers;

use App\Models\classes;
use App\Models\guruMataPelajaran;
use Storage;
use App\Models\schools;
use App\Models\Scores;
use App\Models\students;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class SpreadsheetController extends Controller
{
    public function export($siswa_id)
    {
        $sekolah = schools::first();
        $siswa = students::where('id', $siswa_id)->first();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('/app/public/templates/template.xlsx'));

        // sheet sampul
        $worksheet = $spreadsheet->getSheetByName('Sampul');

        $worksheet->getCell('B5')->setValue($sekolah->school_name);
        $worksheet->getCell('B21')->setValue($siswa->name);
        $worksheet->getCell('B22')->setValue($siswa->nis . "/" . $siswa->nisn);
        
        // sheet keterangan
        $worksheet = $spreadsheet->getSheetByName('keterangan');

        $worksheet->getCell('A4')->setValue($sekolah->school_name);
        $worksheet->getCell('E7')->setValue($sekolah->school_name);
        $worksheet->getCell('E8')->setValue($sekolah->npsn);
        $worksheet->getCell('E9')->setValueExplicit($sekolah->nss, DataType::TYPE_STRING);
        $worksheet->getCell('E10')->setValue($sekolah->address);
        $worksheet->getCell('E11')->setValue($sekolah->kelurahan);
        $worksheet->getCell('E12')->setValue($sekolah->kecamatan);
        $worksheet->getCell('E13')->setValue($sekolah->kabupaten);
        $worksheet->getCell('E14')->setValue($sekolah->website);
        $worksheet->getCell('E15')->setValue($sekolah->email);

        // sheet id pelajar

        $worksheet = $spreadsheet->getSheetByName('id pelajar');

        $worksheet->getCell('E4')->setValue($siswa->name); // Nama Peserta Didik
        $worksheet->getCell('E5')->setValue($siswa->nis); // NIS
        $worksheet->getCell('E6')->setValue($siswa->nisn); // NISN
        $worksheet->getCell('E7')->setValue($siswa->birth_place . ', ' . $siswa->birth_date); // Tempat, Tanggal Lahir
        $worksheet->getCell('E8')->setValue($siswa->gender); // Jenis Kelamin
        $worksheet->getCell('E9')->setValue($siswa->religion); // Agama
        $worksheet->getCell('E10')->setValue($siswa->family_status); // Status Dalam Keluarga
        $worksheet->getCell('E11')->setValue($siswa->child_order); // Anak ke-
        $worksheet->getCell('E12')->setValue($siswa->address); // Alamat Peserta Didik
        $worksheet->getCell('E13')->setValue($siswa->origin_school); // Sekolah Asal
        $worksheet->getCell('E15')->setValue($siswa->registration_status); // Status Pendaftaran - Status
        $worksheet->getCell('E16')->setValue($siswa->accepted_in_class); // Status Pendaftaran - Diterima di Kelas
        $worksheet->getCell('E17')->setValue($siswa->admission_date); // Status Pendaftaran - Pada Tanggal
        $worksheet->getCell('E19')->setValue($siswa->father_name); // Nama Orangtua - Ayah
        $worksheet->getCell('E20')->setValue($siswa->mother_name); // Nama Orangtua - Ibu
        $worksheet->getCell('E22')->setValue($siswa->father_job); // Pekerjaan Orangtua - Ayah
        $worksheet->getCell('E23')->setValue($siswa->mother_job); // Pekerjaan Orangtua - Ibu
        $worksheet->getCell('E24')->setValue($siswa->parent_address); // Alamat Orangtua
        $worksheet->getCell('E25')->setValue($siswa->parent_phone); // No. HP Orangtua
        $worksheet->getCell('E27')->setValue($siswa->guardian_name); // Wali Peserta Didik - Nama Wali
        $worksheet->getCell('E28')->setValue($siswa->guardian_job); // Wali Peserta Didik - Pekerjaan Wali
        $worksheet->getCell('E29')->setValue($siswa->guardian_address); // Wali Peserta Didik - Alamat Wali
        $worksheet->getCell('E30')->setValue($siswa->guardian_phone); // Wali Peserta Didik - No. HP Wali
        $worksheet->getCell('G32')->setValue($sekolah->kelurahan);
        $worksheet->getCell('G34')->setValue($sekolah->school_name);

        // raport

        $worksheet = $spreadsheet->getSheetByName('raport');

        $worksheet->getCell('C1')->setValue($sekolah->school_name);
        $worksheet->getCell('C2')->setValue($sekolah->address);
        $worksheet->getCell('C3')->setValue($siswa->name);
        $worksheet->getCell('C4')->setValue($siswa->nis . "/" . $siswa->nisn);
        $worksheet->getCell('H1')->setValue($siswa->class->class_name);
        $worksheet->getCell('H2')->setValue($sekolah->semester->name);
        $worksheet->getCell('H3')->setValue($sekolah->academicYear->name);

        $row = 9;
        $worksheet->setCellValue('B' . $row, 'Beriman, bertakwa kepada Tuhan Yang Maha Esa, dan berakhlak mulia');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->faith_and_piety);

        $row++;
        $worksheet->setCellValue('B' . $row, 'Mandiri');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->independent);

        $row++;
        $worksheet->setCellValue('B' . $row, 'Bergotong royong');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->teamwork);

        $row++;
        $worksheet->setCellValue('B' . $row, 'Kreatif');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->creative);

        $row++;
        $worksheet->setCellValue('B' . $row, 'Bernalar kritis');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->critical_thinking);

        $row++;
        $worksheet->setCellValue('B' . $row, 'Berkebinekaan global');
        $worksheet->setCellValue('D' . $row, $siswa->sikap->global_diversity);

        
        $filename = $siswa->name;

        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function exportScore($id) // id kelas
    {
        $class = classes::where('id', $id)->first();
        $sekolah = schools::first();
        $tahunAjaran = $sekolah->academicYear;
        $semester = $sekolah->semester;
        $teacher = auth('teacher')->user();
        $subject = guruMataPelajaran::where('teacher_id', $teacher->id)->first()->subject;

        // scores

        $scores = Scores::where('class_id', $class->id)->where('subject_id', $subject->id)->where('teacher_id', $teacher->id)->where('academic_year_id', $tahunAjaran->id)->where('semester_id', $semester->id)->get();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(storage_path('/app/public/templates/template 2.xlsx'));

        // sheet sampul
        $worksheet = $spreadsheet->getActiveSheet();

        // data umum

        $worksheet->getCell('B3')->setValue($sekolah->school_name);
        $worksheet->getCell('B4')->setValue('TAHUN AJARAN ' . $tahunAjaran->name);
        $worksheet->getCell('F6')->setValue($class->class_name);
        $worksheet->getCell('F7')->setValue($semester->name);
        $worksheet->getCell('F8')->setValue($subject->subject_name);
        $worksheet->getCell('F9')->setValue($teacher->name);

        $no = 1;
        $index = 14;

        foreach ($scores as $score) {
            $worksheet->getCell('B' . $index)->setValue($no);
            $worksheet->getCell('C' . $index)->setValueExplicit($score->student->nis, DataType::TYPE_STRING);
            $worksheet->getCell('D' . $index)->setValueExplicit($score->student->nisn, DataType::TYPE_STRING);
            $worksheet->getCell('F' . $index)->setValue($score->student->name);
            $worksheet->getCell('G' . $index)->setValue($score->score);
            $worksheet->getCell('H' . $index)->setValue($score->teacher_notes);

            $no++;
            $index++;
        }

        $filename = $class->class_name;

        // redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }
}
