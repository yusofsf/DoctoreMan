<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\User;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    public function getTitle(): string
    {
        return 'زمان بندی جدید';
    }

    public function getBreadcrumb(): string
    {
        return 'ایجاد';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ((empty($data['end_time']) || !$data['end_time']) && !empty($data['start_time']) && !empty($data['user_id'])) {
            $user = User::find($data['user_id']);
            $consultationMinutes = $user?->doctor?->consultation_duration ?? 30;

            try {
                $start = Carbon::parse($data['start_time']);
                $data['end_time'] = $start->copy()->addMinutes((int) $consultationMinutes)->format('H:i:s');
            } catch (\Throwable $e) {
            }
        }

        return $data;
    }
}
