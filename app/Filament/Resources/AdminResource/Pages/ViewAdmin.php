<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAdmin extends ViewRecord
{
    protected static string $resource = AdminResource::class;

    public function getTitle(): string
    {
        return 'مشاهده ادمین';
    }

    public function getBreadcrumb(): string
    {
        return 'مشاهده';
    }
}

