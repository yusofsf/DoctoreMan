<?php

namespace App\Enums;

enum DayOfWeek: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    // متد کمکی برای تبدیل نام به enum
    public static function fromName(string $name): ?self
    {
        return match(strtolower($name)) {
            'saturday' => self::SATURDAY,
            'sunday' => self::SUNDAY,
            'monday' => self::MONDAY,
            'tuesday' => self::TUESDAY,
            'wednesday' => self::WEDNESDAY,
            'thursday' => self::THURSDAY,
            'friday' => self::FRIDAY,
            default => null,
        };
    }

    // برای نمایش فارسی
    public function getLabel(): string
    {
        return match($this) {
            self::SATURDAY => 'شنبه',
            self::SUNDAY => 'یکشنبه',
            self::MONDAY => 'دوشنبه',
            self::TUESDAY => 'سه‌شنبه',
            self::WEDNESDAY => 'چهارشنبه',
            self::THURSDAY => 'پنج‌شنبه',
            self::FRIDAY => 'جمعه',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])
            ->toArray();
    }
}