<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        SystemSetting::updateOrCreate(['id' => 1], [
            'system_name' => 'Chriss Integrated Systems',
            'system_short_name' => 'CIS',
            'system_logo' => null,
            'system_favicon' => null,
            'primary_color' => '#1a73e8',
            'secondary_color' => '#6c757d',
            'accent_color' => '#0d6efd',
            'login_background' => null,
            'currency' => 'TZS',
            'timezone' => 'Africa/Dar_es_Salaam',
            'email' => 'info@chrissintegrated.com',
            'phone' => '+255 000 000 000',
            'address' => 'Tanzania',
            'footer_text' => 'All rights reserved.',
            'email_from_name' => 'Chriss Integrated Systems',
            'email_from_address' => 'noreply@chrissintegrated.com',
            'maintenance_mode' => false,
        ]);

        // Clear the cache after seeding
        SystemSetting::clearCache();
    }
}