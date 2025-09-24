<?php

namespace App\Enums;

enum UserRole : string
{
    case DOCTOR = 'دکتر';
    case ADMINISTRATOR = 'ادمین';
    case PATIENT = 'بیمار';

    public function getLabel(): string
    {
        return $this->value;
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
