<?php

namespace Tests\Unit;

use App\Models\Classes;
use App\Models\Students;
use App\Services\Telegram\RuleBasedTelegramIntentParser;
use App\Services\Telegram\TelegramIntent;
use Illuminate\Support\Collection;
use Tests\TestCase;

class RuleBasedTelegramIntentParserTest extends TestCase
{
    private RuleBasedTelegramIntentParser $parser;

    /**
     * @var Collection<int, Classes>
     */
    private Collection $classes;

    protected function setUp(): void
    {
        parent::setUp();

        $this->parser = new RuleBasedTelegramIntentParser;

        $class = new Classes(['name' => 'VII A']);
        $class->id = 1;

        $budi = new Students(['name' => 'Budi Santoso', 'status' => 'ACTIVE']);
        $budi->id = 11;

        $siti = new Students(['name' => 'Siti Aminah', 'status' => 'ACTIVE']);
        $siti->id = 12;

        $class->setRelation('students', collect([$budi, $siti]));

        $this->classes = collect([$class]);
    }

    public function test_it_detects_lowest_attendance_and_class(): void
    {
        $intent = $this->parser->parse('siswa yang kehadirannya paling rendah di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::LOWEST_ATTENDANCE, $intent->type);
        $this->assertSame(1, $intent->classId);
    }

    public function test_it_uses_context_for_kelas_ini(): void
    {
        $intent = $this->parser->parse('siapa yang paling sering alfa di kelas ini', $this->classes, 1);

        $this->assertSame(TelegramIntent::MOST_ABSENT, $intent->type);
        $this->assertSame(1, $intent->classId);
    }

    public function test_it_detects_student_attendance(): void
    {
        $intent = $this->parser->parse('kehadiran Budi', $this->classes);

        $this->assertSame(TelegramIntent::STUDENT_ATTENDANCE, $intent->type);
        $this->assertSame(11, $intent->studentId);
    }

    public function test_it_detects_assessment_ranking(): void
    {
        $intent = $this->parser->parse('nilai PTS terendah di 7A', $this->classes);

        $this->assertSame(TelegramIntent::ASSESSMENT_RANKING, $intent->type);
        $this->assertSame(TelegramIntent::ASSESSMENT_MIDTERM, $intent->assessmentType);
        $this->assertFalse($intent->descending);
        $this->assertSame(1, $intent->classId);
    }

    public function test_it_detects_full_recap(): void
    {
        $intent = $this->parser->parse('rekap lengkap kelas VII A', $this->classes);

        $this->assertSame(TelegramIntent::FULL_RECAP, $intent->type);
        $this->assertTrue($intent->full);
    }

    public function test_it_detects_student_score_detail(): void
    {
        $intent = $this->parser->parse('nilai Budi', $this->classes);

        $this->assertSame(TelegramIntent::STUDENT_SCORE, $intent->type);
        $this->assertSame(11, $intent->studentId);
    }

    public function test_it_detects_class_list(): void
    {
        $intent = $this->parser->parse('kelas apa saja yang saya punya', $this->classes);

        $this->assertSame(TelegramIntent::LIST_CLASSES, $intent->type);
    }

    public function test_it_extracts_ranking_limit(): void
    {
        $intent = $this->parser->parse('top 3 siswa dengan kehadiran terendah di 7A', $this->classes);

        $this->assertSame(TelegramIntent::LOWEST_ATTENDANCE, $intent->type);
        $this->assertSame(3, $intent->limit);
    }

    public function test_it_detects_highest_average(): void
    {
        $intent = $this->parser->parse('nilai rata-rata tertinggi di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::HIGHEST_AVERAGE, $intent->type);
    }

    public function test_it_detects_below_kkm(): void
    {
        $intent = $this->parser->parse('siapa yang nilainya di bawah KKM di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::BELOW_KKM, $intent->type);
        $this->assertSame(1, $intent->classId);
    }

    public function test_it_detects_missing_scores(): void
    {
        $intent = $this->parser->parse('siapa yang belum punya nilai UH di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::MISSING_SCORES, $intent->type);
        $this->assertSame(TelegramIntent::ASSESSMENT_DAILY_TEST, $intent->assessmentType);
    }

    public function test_it_detects_at_risk_students(): void
    {
        $intent = $this->parser->parse('siswa yang perlu perhatian di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::AT_RISK, $intent->type);
    }

    public function test_it_detects_search(): void
    {
        $intent = $this->parser->parse('cari Budi', $this->classes);

        $this->assertSame(TelegramIntent::SEARCH_STUDENT, $intent->type);
        $this->assertSame('budi', $intent->searchTerm);
    }

    public function test_it_detects_export_attendance(): void
    {
        $intent = $this->parser->parse('unduh rekap absensi kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::EXPORT_ATTENDANCE, $intent->type);
        $this->assertSame(1, $intent->classId);
    }

    public function test_it_detects_compare_classes(): void
    {
        $otherClass = new Classes(['name' => 'VII B']);
        $otherClass->id = 2;
        $otherClass->setRelation('students', collect());

        $intent = $this->parser->parse(
            'bandingkan kelas 7A dan 7B',
            $this->classes->push($otherClass)
        );

        $this->assertSame(TelegramIntent::COMPARE_CLASSES, $intent->type);
        $this->assertSame(1, $intent->classId);
        $this->assertSame(2, $intent->secondaryClassId);
    }

    public function test_it_detects_attendance_period(): void
    {
        $intent = $this->parser->parse('siapa yang paling sering alfa bulan ini di kelas 7A', $this->classes);

        $this->assertSame(TelegramIntent::MOST_ABSENT, $intent->type);
        $this->assertSame('this_month', $intent->period);
    }
}
