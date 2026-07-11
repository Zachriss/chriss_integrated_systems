<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\ProviderFeeRule;
use App\Models\CommissionRule;
use App\Models\CashPoint;
use App\Models\CashOpening;
use App\Models\StaffCashAssignment;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CashPointSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1; // Assuming admin user exists

        // 1. Create Providers
        $providers = [
            ['name' => 'Cash (Treasury)', 'code' => 'CASH'],
            ['name' => 'Airtel Money', 'code' => 'AIRTEL'],
            ['name' => 'M-Pesa', 'code' => 'MPESA'],
            ['name' => 'HaloPesa', 'code' => 'HALOPESA'],
            ['name' => 'MixBy Yas', 'code' => 'MIXBY'],
        ];

        foreach ($providers as $p) {
            Provider::create([
                'name' => $p['name'],
                'code' => $p['code'],
                'status' => 'active',
                'created_by' => $adminId,
            ]);
        }

        // 2. Create Fee Rules for each provider
        $providerIds = Provider::pluck('id', 'code');

        $feeRules = [
            'AIRTEL' => [
                ['deposit', 0, 5000, 100],
                ['deposit', 5001, 10000, 200],
                ['deposit', 10001, 50000, 500],
                ['deposit', 50001, null, 1000],
                ['withdraw', 0, 5000, 150],
                ['withdraw', 5001, 10000, 300],
                ['withdraw', 10001, 50000, 750],
                ['withdraw', 50001, null, 1500],
            ],
            'MPESA' => [
                ['deposit', 0, 5000, 0],
                ['deposit', 5001, 10000, 0],
                ['deposit', 10001, 50000, 0],
                ['deposit', 50001, null, 0],
                ['withdraw', 0, 5000, 200],
                ['withdraw', 5001, 10000, 400],
                ['withdraw', 10001, 50000, 1000],
                ['withdraw', 50001, null, 2000],
            ],
            'HALOPESA' => [
                ['deposit', 0, 5000, 50],
                ['deposit', 5001, 10000, 100],
                ['deposit', 10001, 50000, 250],
                ['deposit', 50001, null, 500],
                ['withdraw', 0, 5000, 100],
                ['withdraw', 5001, 10000, 200],
                ['withdraw', 10001, 50000, 500],
                ['withdraw', 50001, null, 1000],
            ],
            'MIXBY' => [
                ['deposit', 0, 5000, 75],
                ['deposit', 5001, 10000, 150],
                ['deposit', 10001, 50000, 350],
                ['deposit', 50001, null, 700],
                ['withdraw', 0, 5000, 125],
                ['withdraw', 5001, 10000, 250],
                ['withdraw', 10001, 50000, 600],
                ['withdraw', 50001, null, 1200],
            ],
        ];

        foreach ($feeRules as $code => $rules) {
            $providerId = $providerIds[$code];
            foreach ($rules as $rule) {
                ProviderFeeRule::create([
                    'provider_id' => $providerId,
                    'transaction_type' => $rule[0],
                    'min_amount' => $rule[1],
                    'max_amount' => $rule[2],
                    'fee_amount' => $rule[3],
                    'status' => 'active',
                    'created_by' => $adminId,
                ]);
            }
        }

        // 3. Create Commission Rules
        $commissionRules = [
            'AIRTEL' => [70, 30],
            'MPESA' => [60, 40],
            'HALOPESA' => [75, 25],
            'MIXBY' => [65, 35],
        ];

        foreach ($commissionRules as $code => $percentages) {
            CommissionRule::create([
                'provider_id' => $providerIds[$code],
                'agent_percentage' => $percentages[0],
                'system_percentage' => $percentages[1],
                'status' => 'active',
                'created_by' => $adminId,
            ]);
        }

        // 4. Create Cash Points
        $cashPoints = ['Main Branch', 'Downtown Kiosk', 'Market Street'];
        foreach ($cashPoints as $cp) {
            CashPoint::create([
                'name' => $cp,
                'status' => 'active',
            ]);
        }
    }
}