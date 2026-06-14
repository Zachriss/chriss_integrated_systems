<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Software Development', 'description' => 'Custom software, web applications, and database development.'],
            ['name' => 'Website Development', 'description' => 'Custom websites, e-commerce, and CMS development.'],
            ['name' => 'Mobile App Development', 'description' => 'Android and iOS app development services.'],
            ['name' => 'Networking', 'description' => 'LAN setup, router configuration, and secure office networks.'],
            ['name' => 'Wi-Fi Installation', 'description' => 'Hotspot packages, Wi-Fi setup, and connectivity support.'],
            ['name' => 'CCTV Installation', 'description' => 'Security camera installation and surveillance systems.'],
            ['name' => 'Electrical Services', 'description' => 'Installations, maintenance, and electrical support.'],
            ['name' => 'Printing Services', 'description' => 'Printing, photocopying, binding, laminating, and supplies.'],
            ['name' => 'Computer Repair', 'description' => 'Computer, printer, phone, and device troubleshooting.'],
            ['name' => 'Online Applications', 'description' => 'Government, academic, and business application assistance.'],
            ['name' => 'Cash Point Services', 'description' => 'Mobile money and payment assistance services.'],
            ['name' => 'IT Consulting', 'description' => 'Technology advisory and digital transformation consulting.'],
        ];

        foreach ($categories as $category) {
            ServiceCategory::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($category['name'])],
                $category
            );
        }
    }
}