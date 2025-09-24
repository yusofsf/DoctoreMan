<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSchedule extends ViewRecord
{
    protected static string $resource = ScheduleResource::class;

    public function getTitle(): string
    {
        return 'مشاهده زمان بندی';
    }

    public function getBreadcrumb(): string
    {
        return 'مشاهده';
    }
}

