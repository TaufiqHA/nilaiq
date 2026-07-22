<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AssignmentMeetingsController;
use App\Http\Controllers\AssignmentScoresController;
use App\Http\Controllers\AttendanceMeetingsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatatanWaliKelasController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\ClassWaliKelasController;
use App\Http\Controllers\DailyTestMeetingsController;
use App\Http\Controllers\DailyTestScoresController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EkskulController;
use App\Http\Controllers\FinalExamsController;
use App\Http\Controllers\FinalScoresController;
use App\Http\Controllers\MapelSettingsController;
use App\Http\Controllers\MidtermExamsController;
use App\Http\Controllers\MidtermScoresController;
use App\Http\Controllers\NilaiMapelController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\RaportController;
use App\Http\Controllers\RecapsController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\RekapNilaiController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SettingsWaliKelasController;
use App\Http\Controllers\SikapController;
use App\Http\Controllers\StudentsController;
use App\Http\Controllers\StudentWaliKelasController;
use App\Http\Middleware\WaliKelasMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('login');
    })->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Auth & Account
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Academic Years
    Route::resource('academic-years', AcademicYearController::class);
    Route::delete('academic-years/{academic_year}/delete', [AcademicYearController::class, 'delete'])->name('academic-years.delete');

    // Settings Wali Kelas
    Route::resource('settings-wali-kelas', SettingsWaliKelasController::class)->parameters(['settings-wali-kelas' => 'settings_wali_kelas']);
    Route::delete('settings-wali-kelas/{settings_wali_kelas}/delete', [SettingsWaliKelasController::class, 'delete'])->name('settings-wali-kelas.delete');

    // Guru Mapel Routes Group
    Route::middleware(['mapel'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data & General Settings
        Route::get('/master-data', [SettingsController::class, 'index'])->name('master-data.index');
        Route::post('/master-data', [SettingsController::class, 'store'])->name('master-data.store');
        Route::resource('settings', SettingsController::class);
        Route::delete('settings/{setting}/delete', [SettingsController::class, 'delete'])->name('settings.delete');

        Route::resource('classes', ClassesController::class);
        Route::delete('classes/{class}/delete', [ClassesController::class, 'delete'])->name('classes.delete');

        Route::post('students/import', [StudentsController::class, 'import'])->name('students.import');
        Route::resource('students', StudentsController::class);
        Route::delete('students/{student}/delete', [StudentsController::class, 'delete'])->name('students.delete');

        // Attendance Management
        Route::resource('attendance-meetings', AttendanceMeetingsController::class);
        Route::delete('attendance-meetings/{attendance_meeting}/delete', [AttendanceMeetingsController::class, 'delete'])->name('attendance-meetings.delete');

        Route::resource('attendances', AttendancesController::class);
        Route::delete('attendances/{attendance}/delete', [AttendancesController::class, 'delete'])->name('attendances.delete');

        // Daily Tests Management
        Route::resource('daily-test-meetings', DailyTestMeetingsController::class);
        Route::delete('daily-test-meetings/{daily_test_meeting}/delete', [DailyTestMeetingsController::class, 'delete'])->name('daily-test-meetings.delete');

        Route::resource('daily-test-scores', DailyTestScoresController::class);
        Route::delete('daily-test-scores/{daily_test_score}/delete', [DailyTestScoresController::class, 'delete'])->name('daily-test-scores.delete');

        // Assignment Management
        Route::resource('assignment-meetings', AssignmentMeetingsController::class);
        Route::delete('assignment-meetings/{assignment_meeting}/delete', [AssignmentMeetingsController::class, 'delete'])->name('assignment-meetings.delete');

        Route::resource('assignment-scores', AssignmentScoresController::class);
        Route::delete('assignment-scores/{assignment_score}/delete', [AssignmentScoresController::class, 'delete'])->name('assignment-scores.delete');

        // Exams Management
        Route::resource('midterm-exams', MidtermExamsController::class);
        Route::delete('midterm-exams/{midterm_exam}/delete', [MidtermExamsController::class, 'delete'])->name('midterm-exams.delete');

        Route::resource('final-exams', FinalExamsController::class);
        Route::delete('final-exams/{final_exam}/delete', [FinalExamsController::class, 'delete'])->name('final-exams.delete');

        Route::resource('midterm-scores', MidtermScoresController::class);
        Route::delete('midterm-scores/{midterm_score}/delete', [MidtermScoresController::class, 'delete'])->name('midterm-scores.delete');

        Route::resource('final-scores', FinalScoresController::class);
        Route::delete('final-scores/{final_score}/delete', [FinalScoresController::class, 'delete'])->name('final-scores.delete');

        // Rekap Data Routes
        Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
        Route::get('/rekap/data/{class}', [RekapController::class, 'getClassRekapData'])->name('rekap.data');
        Route::get('/rekap-absensi', [AttendanceMeetingsController::class, 'rekapAbsensi'])->name('rekap-absensi.index');

        // Recaps Management
        Route::get('/nilai-akhir', [RecapsController::class, 'nilaiAkhirView'])->name('nilai-akhir.index');
        Route::post('recaps/batch', [RecapsController::class, 'batchStore'])->name('recaps.batch');
        Route::resource('recaps', RecapsController::class);
        Route::delete('recaps/{recap}/delete', [RecapsController::class, 'delete'])->name('recaps.delete');
    });

    // Wali Kelas Routes Group
    Route::middleware([WaliKelasMiddleware::class])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'waliKelas'])->name('dashboard');

        Route::resource('mapel-settings', MapelSettingsController::class)->parameters(['mapel-settings' => 'mapel_setting']);
        Route::delete('mapel-settings/{mapel_setting}/delete', [MapelSettingsController::class, 'delete'])->name('mapel-settings.delete');

        Route::resource('class-wali-kelas', ClassWaliKelasController::class)->parameters(['class-wali-kelas' => 'class_wali_kelas']);
        Route::delete('class-wali-kelas/{class_wali_kelas}/delete', [ClassWaliKelasController::class, 'delete'])->name('class-wali-kelas.delete');
        Route::get('/informasi-kelas', [ClassWaliKelasController::class, 'index'])->name('informasi-kelas');

        Route::post('student-wali-kelas/import', [StudentWaliKelasController::class, 'import'])->name('student-wali-kelas.import');
        Route::resource('student-wali-kelas', StudentWaliKelasController::class)->parameters(['student-wali-kelas' => 'student_wali_kelas']);
        Route::delete('student-wali-kelas/{student_wali_kelas}/delete', [StudentWaliKelasController::class, 'delete'])->name('student-wali-kelas.delete');
        Route::get('/siswa', [StudentWaliKelasController::class, 'index'])->name('siswa');

        Route::resource('ekskuls', EkskulController::class);
        Route::delete('ekskuls/{ekskul}/delete', [EkskulController::class, 'delete'])->name('ekskuls.delete');
        Route::get('/ekstrakurikuler', [EkskulController::class, 'index'])->name('ekstrakurikuler');

        Route::resource('prestasis', PrestasiController::class);
        Route::delete('prestasis/{prestasi}/delete', [PrestasiController::class, 'delete'])->name('prestasis.delete');
        Route::get('/prestasi', [PrestasiController::class, 'index'])->name('prestasi');

        Route::resource('sikaps', SikapController::class);
        Route::delete('sikaps/{sikap}/delete', [SikapController::class, 'delete'])->name('sikaps.delete');
        Route::get('/sikap', [SikapController::class, 'index'])->name('sikap');

        Route::resource('absensis', AbsensiController::class);
        Route::delete('absensis/{absensi}/delete', [AbsensiController::class, 'delete'])->name('absensis.delete');
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi');

        Route::resource('catatan-wali-kelas', CatatanWaliKelasController::class)->parameters(['catatan-wali-kelas' => 'catatan_wali_kelas']);
        Route::delete('catatan-wali-kelas/{catatan_wali_kelas}/delete', [CatatanWaliKelasController::class, 'delete'])->name('catatan-wali-kelas.delete');
        Route::get('/catatan-wali-kelas-view', [CatatanWaliKelasController::class, 'index'])->name('catatan-wali-kelas');

        Route::resource('nilai-mapels', NilaiMapelController::class)->parameters(['nilai-mapels' => 'nilai_mapel']);
        Route::post('nilai-mapels/batch', [NilaiMapelController::class, 'batchStore'])->name('nilai-mapels.batch');
        Route::delete('nilai-mapels/{nilai_mapel}/delete', [NilaiMapelController::class, 'delete'])->name('nilai-mapels.delete');
        Route::get('/nilai-mapel', [NilaiMapelController::class, 'index'])->name('nilai-mapel');

        // Raport Routes
        Route::get('/raport', [RaportController::class, 'index'])->name('raport');
        Route::post('/raport/mapel-kelompok', [RaportController::class, 'updateKelompok'])->name('raport.update-kelompok');
        Route::get('/raport/cetak-semua', [RaportController::class, 'cetakSemua'])->name('raport.cetak-semua');
        Route::get('/raport/{student}/cetak', [RaportController::class, 'cetak'])->name('raport.cetak');

        // Rekap Nilai Route
        Route::get('/rekap-nilai', [RekapNilaiController::class, 'index'])->name('rekap-nilai');
    });
});
