<?php

namespace App\Rules;

use App\Models\Appointment;
use Carbon\Carbon;
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

        // بررسی اینکه دکتر ساعات کاری دارد یا نه
        if (!$doctor->work_start_time || !$doctor->work_end_time) {
            $fail('ساعات کاری دکتر تعریف نشده است.');
            return;
        }

        // تبدیل زمان‌ها به فرمت قابل مقایسه
        $startTime = Carbon::parse($value);
        $workStart = Carbon::parse($doctor->work_start_time);
        $workEnd = Carbon::parse($doctor->work_end_time);

        // بررسی اینکه زمان شروع در بازه ساعات کاری است یا نه
        if ($startTime->lt($workStart) || $startTime->gt($workEnd)) {
            $fail("زمان شروع باید بین ساعات کاری دکتر ({$workStart->format('H:i')} تا {$workEnd->format('H:i')}) باشد.");
        }
    }
}