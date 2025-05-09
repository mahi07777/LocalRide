<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $packages = [
            [
                'name' => '10 km / hr',
                'max_km' => 10,
                'max_hours' => 1,
                'base_price' => 500,
                'extra_km_price' => 20,
                'extra_hr_price' => 100,
            ],
            [
                'name' => '20 km / hr',
                'max_km' => 20,
                'max_hours' => 2,
                'base_price' => 800,
                'extra_km_price' => 10,
                'extra_hr_price' => 90,
            ]
            ];

            foreach ($packages as $package) {
                Package::create($package);
            }
    }
}
