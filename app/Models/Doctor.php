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

        return isset($workingDays[$dayOfWeek->value]) &&
            ($workingDays[$dayOfWeek->value]['is_working'] ?? false);
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

        return $workingDays[$dayOfWeek->value] ?? [
            'is_working' => false,
            'start_time' => null,
            'end_time' => null,
        ];
    }

    /**
     * Get all working days
     *
     * @return array
     */


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

    public function isWithinWorkHours(string $time): bool
    {
        if (!$this->work_start_time || !$this->work_end_time) {
            return false;
        }

        $checkTime = \Carbon\Carbon::parse($time);
        $startTime = \Carbon\Carbon::parse($this->work_start_time);
        $endTime = \Carbon\Carbon::parse($this->work_end_time);

        return $checkTime->between($startTime, $endTime);
    }

    protected function casts(): array
    {
        return [
            'working_days' => 'array',
            'session_duration' => 'integer',
        ];
    }
}
