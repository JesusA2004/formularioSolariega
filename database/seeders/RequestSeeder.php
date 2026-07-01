<?php

namespace Database\Seeders;

use App\Models\Request;
use Illuminate\Database\Seeder;

class RequestSeeder extends Seeder
{
    /**
     * Run the database seeds. Creates demo requests so the dashboard and
     * reports have data to display out of the box.
     */
    public function run(): void
    {
        Request::factory(40)->create();
    }
}
