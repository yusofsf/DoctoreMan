<?php

namespace App\Rules;

use App\Enums\DayOfWeek;
use App\Models\Appointment;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WithinDoctorWorkDays implements ValidationRule
{
    protected $appointmentId;

    public function __construct($appointmentId)
    {
        $this->appointmentId = $appointmentId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->appointmentId || !$value) {
            return;
        }

        // پیدا کردن نوبت و دکتر مربوطه
        $appointment = Appointment::with('doctor')->find($this->appointmentId);

        if (!$appointment || !$appointment->doctor) {
            $fail('نوبت یا دکتر مربوطه یافت نشد.');
            return;
        }

        $doctor = $appointment->doctor;

        // تبدیل value به DayOfWeek enum
        $selectedDay = $value instanceof DayOfWeek 
            ? $value 
            : (is_string($value) ? DayOfWeek::from($value) : null);

        if (!$selectedDay) {
            $fail('روز هفته نامعتبر است.');
            return;
        }

        // بررسی اینکه آیا روز انتخابی یک روز کاری است
        if (!$doctor->isWorkingDay($selectedDay)) {
            $dayLabel = $selectedDay->getLabel();
            
            // دریافت لیست روزهای کاری
            $workingDaysList = $doctor->getWorkingDays();
            $workingDaysLabels = collect($workingDaysList)->keys()->map(function($dayValue) {
                try {
                    return DayOfWeek::from($dayValue)->getLabel();
                } catch (\Exception $e) {
                    return $dayValue;
                }
            })->implode('، ');

            if (empty($workingDaysLabels)) {
                $fail('روزهای کاری دکتر تعریف نشده است.');
            } else {
                $fail("روز {$dayLabel} در روزهای کاری دکتر نیست. روزهای کاری: {$workingDaysLabels}");
            }
        }
    }
}