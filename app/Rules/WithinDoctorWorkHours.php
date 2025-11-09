<?php

namespace App\Rules;

use App\Enums\DayOfWeek;
use App\Models\Appointment;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WithinDoctorWorkHours implements ValidationRule
{
    protected $appointmentId;
    protected $dayOfWeek;

    public function __construct($appointmentId, $dayOfWeek = null)
    {
        $this->appointmentId = $appointmentId;
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->appointmentId) {
            return;
        }

        // پیدا کردن نوبت و دکتر مربوطه
        $appointment = Appointment::with('doctor')->find($this->appointmentId);

        if (!$appointment || !$appointment->doctor) {
            $fail('نوبت یا دکتر مربوطه یافت نشد.');
            return;
        }

        $doctor = $appointment->doctor;

        // اگر روز هفته مشخص نشده باشد، نمی‌توانیم بررسی کنیم
        if (!$this->dayOfWeek) {
            return;
        }

        $selectedDay = $this->dayOfWeek instanceof DayOfWeek 
            ? $this->dayOfWeek 
            : (is_string($this->dayOfWeek) ? DayOfWeek::from($this->dayOfWeek) : null);

        if (!$selectedDay) {
            return;
        }

        // دریافت ساعات کاری برای روز انتخابی
        $workingHours = $doctor->getWorkingHoursForDay($selectedDay);

        // بررسی اینکه آیا این روز یک روز کاری است
        if (!$workingHours['is_working']) {
            $dayLabel = $selectedDay->getLabel();
            $fail("روز {$dayLabel} در روزهای کاری دکتر نیست.");
            return;
        }

        // بررسی اینکه ساعات کاری برای این روز تعریف شده است
        if (empty($workingHours['start_time']) || empty($workingHours['end_time'])) {
            $fail('ساعات کاری دکتر برای این روز تعریف نشده است.');
            return;
        }

        // تبدیل زمان‌ها به فرمت قابل مقایسه
        try {
            // تبدیل زمان‌ها به Carbon با فرمت مشخص
            $startTime = $this->parseTime($value);
            $workStart = $this->parseTime($workingHours['start_time']);
            $workEnd = $this->parseTime($workingHours['end_time']);

            if (!$startTime || !$workStart || !$workEnd) {
                $fail('فرمت زمان نامعتبر است.');
                return;
            }

            // تنظیم همه زمان‌ها به یک تاریخ ثابت برای مقایسه صحیح
            $baseDate = Carbon::today();
            $startTime->setDate($baseDate->year, $baseDate->month, $baseDate->day);
            $workStart->setDate($baseDate->year, $baseDate->month, $baseDate->day);
            $workEnd->setDate($baseDate->year, $baseDate->month, $baseDate->day);

            // نرمال‌سازی زمان‌ها (تنظیم ثانیه و میکروثانیه به صفر)
            $startTime->second(0)->microsecond(0);
            $workStart->second(0)->microsecond(0);
            $workEnd->second(0)->microsecond(0);

            // بررسی اینکه زمان شروع در بازه ساعات کاری است یا نه
            if ($startTime->lt($workStart) || $startTime->gt($workEnd)) {
                $fail("زمان شروع باید بین ساعات کاری دکتر ({$workStart->format('H:i')} تا {$workEnd->format('H:i')}) باشد.");
            }
        } catch (\Exception $e) {
            $fail('فرمت زمان نامعتبر است.');
        }
    }

    /**
     * Parse time string to Carbon instance
     * 
     * @param string|null $time
     * @return Carbon|null
     */
    private function parseTime($time): ?Carbon
    {
        if (empty($time)) {
            return null;
        }

        // اگر قبلاً Carbon باشد، برگرداندن آن
        if ($time instanceof Carbon) {
            return $time->copy();
        }

        // تبدیل به رشته برای پردازش
        $timeString = (string) $time;

        // تلاش برای پارس با فرمت‌های مختلف
        $formats = ['H:i:s', 'H:i', 'H:i:s.u'];
        
        foreach ($formats as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $timeString);
                // بررسی صحت پارس: اگر زمان پارس شده با رشته اصلی مطابقت دارد
                if ($parsed && $parsed->format($format) === $timeString) {
                    return $parsed;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // اگر هیچ فرمتی کار نکرد، استفاده از parse
        try {
            $parsed = Carbon::parse($timeString);
            // اگر parse موفق بود، فقط بخش زمان را برمی‌گردانیم
            return Carbon::createFromTime(
                $parsed->hour,
                $parsed->minute,
                $parsed->second
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}