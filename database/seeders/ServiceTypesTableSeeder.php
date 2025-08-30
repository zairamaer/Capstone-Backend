<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            ['serviceTypeName' => 'Wash', 'serviceTypeDescription' => 'Basic car wash'],
            ['serviceTypeName' => 'Vacuum', 'serviceTypeDescription' => 'Interior vacuuming'],
            ['serviceTypeName' => 'Armor', 'serviceTypeDescription' => 'Tire and trim dressing'],
            ['serviceTypeName' => 'Handwax', 'serviceTypeDescription' => 'Hand-applied wax'],
            ['serviceTypeName' => 'Buffwax', 'serviceTypeDescription' => 'Machine buffed wax'],
            ['serviceTypeName' => 'Engine Wash', 'serviceTypeDescription' => 'Engine cleaning'],
            ['serviceTypeName' => 'Interior Detailing', 'serviceTypeDescription' => 'Deep interior clean'],
            ['serviceTypeName' => 'Exterior Detailing', 'serviceTypeDescription' => 'Deep exterior clean'],
            ['serviceTypeName' => 'Back to Zero', 'serviceTypeDescription' => 'Full restoration'],
            ['serviceTypeName' => 'Seat Cover', 'serviceTypeDescription' => 'Seat cover cleaning'],
        ];

        DB::table('service_types')->insert($serviceTypes);
    }
}
