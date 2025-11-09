<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Enums\DayOfWeek;
use App\Filament\Resources\DoctorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    public function getTitle(): string
    {
        return 'دکتر جدید';
    }

    public function getBreadcrumb(): string
    {
        return 'ایجاد';
    }
}
