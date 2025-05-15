<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */ public function run()
    {
        Branch::insert([
            [
                'name' => 'Irbid',
                'latitude' => 32.5556,
                'longitude' => 35.8497,
                'address' => 'Irbid, Jordan - Near University Street',
            ],
            [
                'name' => 'Zarqa',
                'latitude' => 32.0728,
                'longitude' => 36.0880,
                'address' => 'Zarqa, Jordan - King Abdullah II Street',
            ],
            [
                'name' => 'Amman',
                'latitude' => 31.9516,
                'longitude' => 35.9239,
                'address' => 'Amman, Jordan - Gardens Street',
            ],
            [
                'name' => 'Aqaba',
                'latitude' => 29.5320,
                'longitude' => 35.0063,
                'address' => 'Aqaba, Jordan - Al-Hammamat Al-Tunisyya Street',
            ],
            [
                'name' => 'Balqa',
                'latitude' => 32.0392,
                'longitude' => 35.7272,
                'address' => 'Balqa, Jordan - Salt Downtown',
            ],
        ]);
    }
}
