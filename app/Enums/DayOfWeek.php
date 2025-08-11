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
    public function persian(): string
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

    // برای ترتیب روزها
    public function order(): int
    {
        return match($this) {
            self::SATURDAY => 0,
            self::SUNDAY => 1,
            self::MONDAY => 2,
            self::TUESDAY => 3,
            self::WEDNESDAY => 4,
            self::THURSDAY => 5,
            self::FRIDAY => 6,
        };
    }
}