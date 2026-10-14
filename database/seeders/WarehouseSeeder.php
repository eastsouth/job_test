<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * 填充初始数据
     *
     * @return void
     */
    public function run(): void
    {
        Warehouse::updateOrCreate(
            ['name' => '一号仓库'],
            ['location' => '广州'],
        );

        Warehouse::updateOrCreate(
            ['name' => '二号仓库'],
            ['location' => '佛山'],
        );
    }
}
