<?php

namespace Database\Seeders;

use App\Enums\AdminStatus;
use App\Models\Admin;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = User::factory()->create([
            'role' => UserRole::ADMINISTRATOR->value,
            'email' => 'admin@example.com',
            'user_name' => 'admin',
            'password' => bcrypt('123456'),
        ]);

        Admin::factory()->create([
            'user_id' => $adminUser->id,
            'status' => AdminStatus::ACTIVE->value,
            'display_name' => 'Super Admin',
        ]);

        $patientUser = User::factory()->create([
            'role' => UserRole::PATIENT->value,
            'email' => 'patient@example.com',
            'user_name' => 'patient',
            'password' => bcrypt('123456')
        ]);

        Patient::factory()->create([
            'user_id' => $patientUser->id,
        ]);
    }
}
