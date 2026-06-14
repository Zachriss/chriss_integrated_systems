<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\StaffProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@chriss.test'],
            [
                'name' => 'Zacharia Christopher',
                'full_name' => 'Zacharia Christopher Sugilo',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@integrated.test'],
            [
                'name' => 'System Admin',
                'full_name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $staffUsers = [
            ['name' => 'IT Staff', 'full_name' => 'IT Department Staff', 'email' => 'it@integrated.test', 'department' => 'IT', 'salary' => 800000],
            ['name' => 'Electrical Staff', 'full_name' => 'Electrical Department Staff', 'email' => 'electrical@integrated.test', 'department' => 'Electrical', 'salary' => 750000],
            ['name' => 'Stationery Staff', 'full_name' => 'Stationery Department Staff', 'email' => 'stationery@integrated.test', 'department' => 'Stationery', 'salary' => 600000],
            ['name' => 'Cashier', 'full_name' => 'Cashier Staff', 'email' => 'cashier@integrated.test', 'department' => 'Cashier', 'salary' => 650000],
            ['name' => 'Networking Staff', 'full_name' => 'Networking Department Staff', 'email' => 'networking@integrated.test', 'department' => 'Networking', 'salary' => 850000],
        ];

        foreach ($staffUsers as $staffData) {
            $staff = User::updateOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'full_name' => $staffData['full_name'],
                    'password' => Hash::make('password'),
                    'role' => 'staff',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            StaffProfile::updateOrCreate(
                ['user_id' => $staff->id],
                ['department' => $staffData['department'], 'salary' => $staffData['salary']]
            );
        }

        $customer = User::updateOrCreate(
            ['email' => 'customer@integrated.test'],
            [
                'name' => 'Demo Customer',
                'full_name' => 'Demo Customer Account',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        for ($i = 1; $i <= 5; $i++) {
            $user = User::updateOrCreate(
                ['email' => "user{$i}@example.test"],
                [
                    'name' => "User {$i}",
                    'full_name' => "Generic User {$i}",
                    'password' => Hash::make('password'),
                    'role' => 'customer',
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
