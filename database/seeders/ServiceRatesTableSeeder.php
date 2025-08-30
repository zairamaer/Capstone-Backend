<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 1, 'price' => 100.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 2, 'price' => 50.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 3, 'price' => 30.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 4, 'price' => 150.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 5, 'price' => 500.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 6, 'price' => 150.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 7, 'price' => 2000.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 8, 'price' => 2000.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 9, 'price' => 400.00],
          ['vehicleSizeCode' => 'S', 'serviceTypeID' => 10, 'price' => 150.00],

          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 1, 'price' => 120.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 2, 'price' => 50.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 3, 'price' => 30.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 4, 'price' => 170.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 5, 'price' => 600.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 6, 'price' => 200.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 7, 'price' => 2500.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 8, 'price' => 2500.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 9, 'price' => 500.00],
          ['vehicleSizeCode' => 'M', 'serviceTypeID' => 10, 'price' => 200.00],

          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 1, 'price' => 150.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 2, 'price' => 50.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 3, 'price' => 30.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 4, 'price' => 200.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 5, 'price' => 700.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 6, 'price' => 250.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 7, 'price' => 3000.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 8, 'price' => 3000.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 9, 'price' => 600.00],
          ['vehicleSizeCode' => 'L', 'serviceTypeID' => 10, 'price' => 250.00],

          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 1, 'price' => 180.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 2, 'price' => 50.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 3, 'price' => 30.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 4, 'price' => 220.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 5, 'price' => 800.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 6, 'price' => 300.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 7, 'price' => 3500.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 8, 'price' => 3500.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 9, 'price' => 700.00],
          ['vehicleSizeCode' => 'XL', 'serviceTypeID' => 10, 'price' => 300.00],

          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 1, 'price' => 200.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 2, 'price' => 50.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 3, 'price' => 30.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 4, 'price' => 250.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 5, 'price' => 1000.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 6, 'price' => 350.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 7, 'price' => 4000.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 8, 'price' => 4000.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 9, 'price' => 800.00],
          ['vehicleSizeCode' => 'XXL', 'serviceTypeID' => 10, 'price' => 350.00],

          ['vehicleSizeCode' => 'MOTOR', 'serviceTypeID' => 1, 'price' => 100.00],
          ['vehicleSizeCode' => 'MOTOR', 'serviceTypeID' => 2, 'price' => 30.00],

          ['vehicleSizeCode' => 'MOTOR/SIDECAR', 'serviceTypeID' => 1, 'price' => 150.00],
          ['vehicleSizeCode' => 'MOTOR/SIDECAR', 'serviceTypeID' => 2, 'price' => 30.00],

          ['vehicleSizeCode' => 'BIG BIKE', 'serviceTypeID' => 1, 'price' => 150.00],
          ['vehicleSizeCode' => 'BIG BIKE', 'serviceTypeID' => 2, 'price' => 30.00],
      ];
        DB::table('service_rates')->insert($rates);
    }
}
