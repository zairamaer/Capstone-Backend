<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            VehicleSizesTableSeeder::class,
            ServiceTypesTableSeeder::class,
            ServiceRatesTableSeeder::class,
        ]);
    }
}
