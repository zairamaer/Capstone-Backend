<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSizesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vehicleSizes = [
            ['vehicleSizeCode' => 'S', 'vehicleSizeDescription' => 'Wigo/Vios/Lancer'],
            ['vehicleSizeCode' => 'M', 'vehicleSizeDescription' => 'Avanza/CRV'],
            ['vehicleSizeCode' => 'L', 'vehicleSizeDescription' => 'Innova/Adventure'],
            ['vehicleSizeCode' => 'XL', 'vehicleSizeDescription' => 'Montero/Everest/Fortuner'],
            ['vehicleSizeCode' => 'XXL', 'vehicleSizeDescription' => 'GL Grandla/Commuter/L300'],
            ['vehicleSizeCode' => 'MOTOR', 'vehicleSizeDescription' => 'Motorcycle'],
            ['vehicleSizeCode' => 'MOTOR/SIDECAR', 'vehicleSizeDescription' => 'Motorcycle with Sidecar'],
            ['vehicleSizeCode' => 'BIG BIKE', 'vehicleSizeDescription' => 'Big Bike'],
        ];

        DB::table('vehicle_sizes')->insert($vehicleSizes);
    }
}
