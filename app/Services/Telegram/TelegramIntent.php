<?php

namespace App\Services\Telegram;

/**
 * Structured intent produced by the rule-based parser.
 */
class TelegramIntent
{
    public const LIST_CLASSES = 'list_classes';

    public const ATTENDANCE_SUMMARY = 'class_attendance_summary';

    public const LOWEST_ATTENDANCE = 'lowest_attendance';

    public const HIGHEST_ATTENDANCE = 'highest_attendance';

    public const MOST_ABSENT = 'most_absent';

    public const STUDENT_ATTENDANCE = 'student_attendance_detail';

    public const SCORE_RECAP = 'class_score_recap';

    public const LOWEST_AVERAGE = 'lowest_average';

    public const HIGHEST_AVERAGE = 'highest_average';

    public const ASSESSMENT_RANKING = 'assessment_type_ranking';

    public const STUDENT_SCORE = 'student_score_detail';

    public const FULL_RECAP = 'full_class_recap';

    public const HELP = 'help';

    public const ASSESSMENT_DAILY_TEST = 'daily_test';

    public const ASSESSMENT_ASSIGNMENT = 'assignment';

    public const ASSESSMENT_MIDTERM = 'midterm';

    public const ASSESSMENT_FINAL = 'final';

    public function __construct(
        public readonly string $type,
        public readonly ?int $classId = null,
        public readonly ?int $studentId = null,
        public readonly int $limit = 5,
        public readonly ?string $assessmentType = null,
        public readonly bool $full = false,
        public readonly bool $descending = false,
    ) {}

    /**
     * Whether the intent needs a selected class to answer.
     */
    public function needsClass(): bool
    {
        return ! in_array($this->type, [self::LIST_CLASSES, self::HELP], true);
    }
}
