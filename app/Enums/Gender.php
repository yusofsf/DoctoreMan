<?php

namespace App\Enums;

enum Gender : string
{
    case MALE = 'مرد';
    case FEMALE = 'زن';

    public function getLabel(): string
    {
        return match ($this) {
            self::MALE => 'مرد',
            self::FEMALE => 'زن'
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}
