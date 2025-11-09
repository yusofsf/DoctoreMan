<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'medical_code',
        'speciality',
        'city',
        'bio',
        'consultation_fee',
        'consultation_duration',
        'working_days',
        'start_time',
        'end_time'
    ];

    public function isWorkingDay(string|DayOfWeek $day): bool
    {
        $dayOfWeek = is_string($day) ? DayOfWeek::fromName($day) : $day;

        if (!$dayOfWeek) {
            return false;
        }

        $workingDays = $this->working_days ?? [];

        // اگر working_days خالی باشد
        if (empty($workingDays)) {
            return false;
        }

        // بررسی اینکه آیا ساختار قدیمی است (آرایه ساده)
        $isSimpleArray = array_keys($workingDays) === range(0, count($workingDays) - 1);
        
        if ($isSimpleArray) {
            // ساختار قدیمی: آرایه ساده مانند ['saturday', 'sunday']
            return in_array($dayOfWeek->value, $workingDays, true);
        }

        // ساختار جدید: JSON با is_working و start_time/end_time
        return isset($workingDays[$dayOfWeek->value]) &&
            (is_array($workingDays[$dayOfWeek->value])
                ? ($workingDays[$dayOfWeek->value]['is_working'] ?? false)
                : (bool)$workingDays[$dayOfWeek->value]);
    }

    /**
     * Get working hours for a specific day
     *
     * @param string|DayOfWeek $day Day name or DayOfWeek enum
     * @return array
     */
    public function getWorkingHoursForDay(string|DayOfWeek $day): array
    {
        $dayOfWeek = is_string($day) ? DayOfWeek::fromName($day) : $day;

        if (!$dayOfWeek) {
            return [
                'is_working' => false,
                'start_time' => null,
                'end_time' => null,
            ];
        }

        $workingDays = $this->working_days ?? [];

        // اگر working_days خالی باشد
        if (empty($workingDays)) {
            return [
                'is_working' => false,
                'start_time' => null,
                'end_time' => null,
            ];
        }

        // بررسی اینکه آیا ساختار قدیمی است (آرایه ساده)
        $keys = array_keys($workingDays);
        $isSimpleArray = !empty($keys) && $keys === range(0, count($workingDays) - 1);
        
        if ($isSimpleArray) {
            // ساختار قدیمی: اگر روز در لیست باشد، از start_time و end_time کلی استفاده کن
            if (in_array($dayOfWeek->value, $workingDays, true)) {
                return [
                    'is_working' => true,
                    'start_time' => $this->start_time ?? '08:00:00',
                    'end_time' => $this->end_time ?? '17:00:00',
                ];
            }
            return [
                'is_working' => false,
                'start_time' => null,
                'end_time' => null,
            ];
        }

        // ساختار جدید: JSON با is_working و start_time/end_time
        $dayData = $workingDays[$dayOfWeek->value] ?? null;

        if ($dayData === null) {
            return [
                'is_working' => false,
                'start_time' => null,
                'end_time' => null,
            ];
        }

        // اگر dayData یک آرایه باشد (ساختار جدید)
        if (is_array($dayData)) {
            return [
                'is_working' => $dayData['is_working'] ?? false,
                'start_time' => $dayData['start_time'] ?? null,
                'end_time' => $dayData['end_time'] ?? null,
            ];
        }

        // اگر dayData یک boolean باشد (ساختار قدیمی دیگر)
        return [
            'is_working' => (bool)$dayData,
            'start_time' => $this->start_time ?? '08:00:00',
            'end_time' => $this->end_time ?? '17:00:00',
        ];
    }

    /**
     * Get all working days
     *
     * @return array
     */
    public function getWorkingDays(): array
    {
        $workingDays = [];

        foreach (DayOfWeek::cases() as $day) {
            if ($this->isWorkingDay($day)) {
                $workingDays[$day->value] = $this->getWorkingHoursForDay($day);
            }
        }

        return $workingDays;
    }

    /**
     * Set working hours for a specific day
     *
     * @param string|DayOfWeek $day
     * @param string $startTime
     * @param string $endTime
     * @param bool $isWorking
     * @return void
     */
    public function setWorkingHours(string|DayOfWeek $day, string $startTime, string $endTime, bool $isWorking = true): void
    {
        $dayOfWeek = is_string($day) ? DayOfWeek::fromName($day) : $day;

        if (!$dayOfWeek) {
            return;
        }

        $workingDays = $this->working_days ?? [];

        $workingDays[$dayOfWeek->value] = [
            'is_working' => $isWorking,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];

        $this->working_days = $workingDays;
    }

    /**
     * Set multiple working days at once
     *
     * @param array $schedule
     * @return void
     */
    public function setWorkingSchedule(array $schedule): void
    {
        $workingDays = [];

        foreach ($schedule as $dayValue => $hours) {
            $dayOfWeek = DayOfWeek::from($dayValue);

            $workingDays[$dayOfWeek->value] = [
                'is_working' => $hours['is_working'] ?? false,
                'start_time' => $hours['start_time'] ?? null,
                'end_time' => $hours['end_time'] ?? null,
            ];
        }

        $this->working_days = $workingDays;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    protected function casts(): array
    {
        return [
            'working_days' => 'array',
            'session_duration' => 'integer',
        ];
    }
}
