<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case RESERVED = 'رزور شده';
    case CANCELLED = 'کنسل شده';
    case APPROVED = 'تایید شده';
    case AVAILABLE = 'قابل رزور';

    public function getLabel(): string
    {
        return match ($this) {
            self::RESERVED => 'رزور شده',
            self::CANCELLED => 'کنسل شده',
            self::APPROVED => 'تایید شده',
            self::AVAILABLE => 'قابل رزور'
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
