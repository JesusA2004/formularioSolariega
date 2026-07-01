<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Nota: no usamos WithoutModelEvents aquí porque el modelo Request
     * depende de su evento "creating" para generar el folio y el uuid.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            RequestSeeder::class,
        ]);
    }
}
