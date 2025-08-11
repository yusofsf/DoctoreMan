<?php

namespace App\Jobs;

use App\Enums\AppointmentStatus;
use App\Enums\DayOfWeek;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateSchedules implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Doctor $doctor
    ) { }

    public $timeout = 300;
    public $tries = 2;

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $this->generateSlots();

            DB::commit();

            Log::info("وقت‌های دکتر {$this->doctor->user->user_name} با موفقیت تولید شد");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("خطا در تولید وقت‌های دکتر {$this->doctor->user->user_name}: " . $e->getMessage());
            throw $e;
        }
    }

    private function generateSlots(): void
    {
        $sessionDuration = $this->doctor->session_duration ?? 30;
        
        // Generate schedules for next 30 days
        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::now()->addDays($i);
            $dayName = strtolower($date->format('l'));

            if ($this->doctor->isWorkingDay($dayName)) {
                $this->generateSlotsForDay($date, $sessionDuration);
            }
        }
    }

    private function generateSlotsForDay(Carbon $date, int $duration): void
    {
        $dayName = strtolower($date->format('l'));
        $workingHours = $this->doctor->getWorkingHoursForDay($dayName);

        if (!$workingHours['is_working']) {
            return;
        }

        $startTime = $workingHours['start_time'];
        $endTime = $workingHours['end_time'];

        $current = Carbon::parse($date->format('Y-m-d') . ' ' . $startTime);
        $end = Carbon::parse($date->format('Y-m-d') . ' ' . $endTime);

        $schedulesToCreate = [];
        $appointmentsToCreate = [];

        while ($current->lt($end)) {
            $slotEnd = $current->copy()->addMinutes($duration);

            if ($slotEnd->lte($end)) {
                $dayOfWeek = DayOfWeek::fromName(strtolower($dayName));
                
                // Prepare schedule data
                $schedulesToCreate[] = [
                    'doctor_id' => $this->doctor->id,
                    'day_of_week' => $dayOfWeek,
                    'start_time' => $current->format('H:i:s'),
                    'end_time' => $slotEnd->format('H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $current->addMinutes($duration);
        }

        if (!empty($schedulesToCreate)) {
            // Bulk insert schedules
            foreach ($schedulesToCreate as $scheduleData) {
                $schedule = Schedule::updateOrCreate([
                    'doctor_id' => $scheduleData['doctor_id'],
                    'day_of_week' => $scheduleData['day_of_week'],
                    'start_time' => $scheduleData['start_time'],
                    'end_time' => $scheduleData['end_time'],
                ], $scheduleData);

                // Prepare appointment data
                $appointmentsToCreate[] = [
                    'date' => $date->format('Y-m-d'),
                    'status' => AppointmentStatus::AVAILABLE,
                    'doctor_id' => $scheduleData['doctor_id'],
                    'schedule_id' => $schedule->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert appointments
            if (!empty($appointmentsToCreate)) {
                Appointment::insert($appointmentsToCreate);
            }
        }
    }
}