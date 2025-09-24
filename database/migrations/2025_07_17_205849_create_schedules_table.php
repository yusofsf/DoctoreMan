<?php

use App\Enums\DayOfWeek;
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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->string('day_of_week');
            } else {
                $table->enum('day_of_week', array_column(DayOfWeek::cases(), 'value'));
            }
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignIdFor(User::class);
            $table->unique(['user_id', 'start_time', 'day_of_week']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
