<?php

namespace App\Filament\Resources\AdminResource\Pages;

use App\Filament\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    public function getTitle(): string
    {
        return 'ادمین جدید';
    }

    public function getBreadcrumb(): string
    {
        return 'ایجاد';
    }
}
