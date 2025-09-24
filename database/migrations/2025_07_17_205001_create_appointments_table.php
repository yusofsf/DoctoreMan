<?php

use App\Enums\AppointmentStatus;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->string('status')->default(AppointmentStatus::AVAILABLE->value);
            } else {
                $table->enum('status', array_column(AppointmentStatus::cases(), 'value'))
                    ->default(AppointmentStatus::AVAILABLE->value);
            }
            $table->text('notes')->nullable();
            $table->foreignIdFor(Doctor::class);
            $table->foreignIdFor(Schedule::class)->nullable();
            $table->foreignIdFor(Patient::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
