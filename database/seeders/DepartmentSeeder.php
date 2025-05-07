<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            'Human Resources',
            'Management',
            'Finance',
            'Information Technology',
            'Sales',
            'Marketing',
            'Customer Support',
            'Legal',
            'Quality Assurance',
            'Research and Development',
            'Logistics',
            'Production',
            'Procurement',
            'Training and Development',
        ];

        foreach ($departments as $department) {
            DB::table('departments')->insert([
                'name' => $department,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
