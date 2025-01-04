<?php

namespace App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource\Pages;

use App\Filament\WaliKelas\Resources\ClassAttendanceSessionsResource;
use Filament\Resources\Pages\Page;

class editClassAttendanceRecords extends Page
{
    protected static string $resource = ClassAttendanceSessionsResource::class;

    protected static string $view = 'filament.wali-kelas.resources.class-attendance-sessions-resource.pages.edit-class-attendance-records';
}
