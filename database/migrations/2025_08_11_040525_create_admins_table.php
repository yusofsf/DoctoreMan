<?php

use App\Enums\AdminStatus;
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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->text('description')->nullable();
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->string('status')->default(AdminStatus::ACTIVE->value);
            } else {
                $table->enum('status', array_column(AdminStatus::cases(), 'value'))
                    ->default(AdminStatus::ACTIVE->value);
            }
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
