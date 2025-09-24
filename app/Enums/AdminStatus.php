<?php

namespace App\Enums;

enum AdminStatus : string
{
    case ACTIVE = 'فعال';
    case INACTIVE = 'غیر فعال';
    case SUSPENDED = 'معلق';

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
