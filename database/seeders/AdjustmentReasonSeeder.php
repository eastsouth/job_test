<?php

namespace Database\Seeders;

use App\Models\AdjustmentReason;
use Illuminate\Database\Seeder;

class AdjustmentReasonSeeder extends Seeder
{
    /**
     * 填充初始数据
     *
     * @return void
     */
    public function run(): void
    {
        $reasons = [
            ['name' => 'Physical count correction', 'is_active' => 1],
            ['name' => 'Damaged items', 'is_active' => 1],
            ['name' => 'Missing items', 'is_active' => 1],
            ['name' => 'Data entry correction', 'is_active' => 0],
        ];

        foreach ($reasons as $reason) {
            AdjustmentReason::updateOrCreate(
                ['name' => $reason['name']],
                $reason,
            );
        }
    }
}
