<?php

use App\Enums\Gender;
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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->string('gender')->default(Gender::MALE->value);
            } else {
                $table->enum('gender', array_column(Gender::cases(), 'value'))
                    ->default(Gender::MALE->value);
            }
            $table->date('birth_date');
            $table->string('phone_number');
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
