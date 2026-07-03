<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Branches ──────────────────────────────────────────────
        $branches = [
            ['name' => 'รามคำแหง',      'is_active' => true],
            ['name' => 'สยามพารากอน',   'is_active' => true],
            ['name' => 'เซ็นทรัลเวิลด์', 'is_active' => true],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        // ── Users (id 1-9) ────────────────────────────────────────
        // id 3  = คนขับ ก  → driver ผูกกับ vehicle กก 1234 กทม.
        // id 4  = branch รามคำแหง    → branch_id = 1
        // id 5  = branch สยามพารากอน → branch_id = 2
        // id 8  = branch เซ็นทรัลเวิลด์ → branch_id = 3
        // id 9  = คนขับ ข  → driver ผูกกับ vehicle ขข 1234 กทม.
        $users = [
            ['name' => 'Admin BH-Logistic',          'email' => 'admin@bh-logistic.com',    'role' => 'admin',  'employee_code' => 'EMP001', 'branch_id' => null],
            ['name' => 'HQ Manager',                  'email' => 'hq@bh-logistic.com',       'role' => 'hq',     'employee_code' => 'EMP002', 'branch_id' => null],
            ['name' => 'คนขับ ก',                     'email' => 'driver1@bh-logistic.com',  'role' => 'driver', 'employee_code' => 'EMP003', 'branch_id' => null],
            ['name' => 'พนักงานสาขา รามคำแหง',        'email' => 'branch1@bh-logistic.com',  'role' => 'branch', 'employee_code' => 'EMP004', 'branch_id' => 1],
            ['name' => 'พนักงานสาขา สยามพารากอน',     'email' => 'branch2@bh-logistic.com',  'role' => 'branch', 'employee_code' => 'EMP005', 'branch_id' => 2],
            ['name' => 'คนขับ ค',                     'email' => 'driver3@bh-logistic.com',  'role' => 'driver', 'employee_code' => 'EMP006', 'branch_id' => null],
            ['name' => 'คนขับ ง',                     'email' => 'driver4@bh-logistic.com',  'role' => 'driver', 'employee_code' => 'EMP007', 'branch_id' => null],
            ['name' => 'พนักงานสาขา เซ็นทรัลเวิลด์',  'email' => 'branch3@bh-logistic.com',  'role' => 'branch', 'employee_code' => 'EMP008', 'branch_id' => 3],
            ['name' => 'คนขับ ข',                     'email' => 'driver2@bh-logistic.com',  'role' => 'driver', 'employee_code' => 'EMP009', 'branch_id' => null],
        ];

        foreach ($users as $data) {
            User::create([
                'name'          => $data['name'],
                'email'         => $data['email'],
                'password'      => Hash::make('password'),
                'role'          => $data['role'],
                'is_active'     => true,
                'employee_code' => $data['employee_code'],
                'branch_id'     => $data['branch_id'],
            ]);
        }

        // ── Vehicles ──────────────────────────────────────────────
        $vehicles = [
            ['license_plate' => 'กก 1234 กทม.', 'driver_id' => 3, 'is_active' => true],
            ['license_plate' => 'ขข 1234 กทม.', 'driver_id' => 9, 'is_active' => true],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}
