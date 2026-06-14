<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create product categories
        $productCategories = [
            ['name' => 'Electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Computer Accessories', 'description' => 'Keyboards, mice, cables, and more'],
            ['name' => 'Networking Equipment', 'description' => 'Routers, switches, and network gear'],
        ];

        foreach ($productCategories as $pc) {
            ProductCategory::firstOrCreate(
                ['slug' => Str::slug($pc['name'])],
                $pc + ['is_active' => true, 'sort_order' => 0]
            );
        }

        $this->command->info('Product categories created: ' . ProductCategory::count());

        // Get category IDs
        $accessories = ProductCategory::where('slug', 'computer-accessories')->first();
        $networking = ProductCategory::where('slug', 'networking-equipment')->first();
        $electronics = ProductCategory::where('slug', 'electronics')->first();

        // Create sample products
        $sampleProducts = [
            ['name' => 'Wireless Mouse', 'sku' => 'WM-001', 'category_id' => $accessories->id, 'buying_price' => 15000, 'selling_price' => 25000, 'quantity' => 20, 'is_featured' => true, 'status' => 'active'],
            ['name' => 'USB-C Hub 7-in-1', 'sku' => 'UC-001', 'category_id' => $accessories->id, 'buying_price' => 30000, 'selling_price' => 45000, 'quantity' => 15, 'is_featured' => true, 'status' => 'active'],
            ['name' => 'WiFi Router 2.4/5GHz', 'sku' => 'WR-001', 'category_id' => $networking->id, 'buying_price' => 40000, 'selling_price' => 65000, 'quantity' => 10, 'is_featured' => true, 'status' => 'active'],
            ['name' => 'HDMI Cable 3m', 'sku' => 'HD-001', 'category_id' => $accessories->id, 'buying_price' => 5000, 'selling_price' => 10000, 'quantity' => 50, 'is_featured' => false, 'status' => 'active'],
            ['name' => 'Laptop Stand Adjustable', 'sku' => 'LS-001', 'category_id' => $accessories->id, 'buying_price' => 20000, 'selling_price' => 35000, 'quantity' => 12, 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Network Switch 8-Port', 'sku' => 'NS-001', 'category_id' => $networking->id, 'buying_price' => 35000, 'selling_price' => 55000, 'quantity' => 8, 'is_featured' => true, 'status' => 'active'],
        ];

        foreach ($sampleProducts as $p) {
            Product::firstOrCreate(
                ['sku' => $p['sku']],
                $p + ['slug' => Str::slug($p['name']), 'low_stock_alert_level' => 5]
            );
        }

        $this->command->info('Products created: ' . Product::count());

        // Get service categories
        $software = ServiceCategory::where('slug', 'software-development')->first();
        $networkingSvc = ServiceCategory::where('slug', 'networking')->first();
        $repair = ServiceCategory::where('slug', 'computer-repair')->first();
        $printing = ServiceCategory::where('slug', 'printing-services')->first();
        $wifi = ServiceCategory::where('slug', 'wi-fi-installation')->first();
        $cctv = ServiceCategory::where('slug', 'cctv-installation')->first();

        // Create sample services
        $sampleServices = [
            ['name' => 'Website Development - Basic', 'category_id' => $software->id, 'base_price' => 350000, 'description' => '5-page responsive website with contact form.', 'short_description' => '5-page responsive website', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Website Development - Premium', 'category_id' => $software->id, 'base_price' => 800000, 'description' => 'Full CMS-based website with blog and admin panel.', 'short_description' => 'CMS website with admin panel', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Network Installation', 'category_id' => $networkingSvc->id, 'base_price' => 200000, 'description' => 'Complete LAN setup for office or home.', 'short_description' => 'LAN installation for office/home', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Computer Repair - Diagnostics', 'category_id' => $repair->id, 'base_price' => 15000, 'description' => 'Diagnostic check for hardware/software issues.', 'short_description' => 'Hardware/software diagnostics', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Computer Repair - Full Service', 'category_id' => $repair->id, 'base_price' => 50000, 'description' => 'Complete repair including parts and labor.', 'short_description' => 'Full repair with parts & labor', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'WiFi Installation - Home', 'category_id' => $wifi->id, 'base_price' => 80000, 'description' => 'Home WiFi setup with router configuration.', 'short_description' => 'Home WiFi setup', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'CCTV Installation - 4 Cameras', 'category_id' => $cctv->id, 'base_price' => 400000, 'description' => '4-camera CCTV system with DVR and installation.', 'short_description' => '4-camera CCTV system', 'is_featured' => true, 'status' => 'active'],
            ['name' => 'Printing Services - B&W', 'category_id' => $printing->id, 'base_price' => 200, 'description' => 'Black and white printing per page.', 'short_description' => 'B&W printing per page', 'is_featured' => false, 'status' => 'active'],
        ];

        foreach ($sampleServices as $s) {
            Service::firstOrCreate(
                ['name' => $s['name']],
                $s + ['duration_hours' => null]
            );
        }

        $this->command->info('Services created: ' . Service::count());
    }
}