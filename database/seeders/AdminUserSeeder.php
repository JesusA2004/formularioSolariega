<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * IMPORTANTE: esta contraseña es solo para el primer acceso en desarrollo.
     * Cámbiala de inmediato en producción desde "Mi perfil" o creando un nuevo
     * usuario administrador y desactivando este.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@solariegacenit.com'],
            [
                'name' => 'Administrador',
                'password' => 'password',
                'role' => UserRole::Admin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
