<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * 填充初始数据
     *
     * @return void
     */
    public function run(): void
    {
        Product::updateOrCreate(
            ['sku' => 'SKU-MOUSE-001'],
            ['name' => '鼠标', 'quantity' => 130],
        );

        Product::updateOrCreate(
            ['sku' => 'SKU-KBD-001'],
            ['name' => '键盘', 'quantity' => 50],
        );

        Product::updateOrCreate(
            ['sku' => 'SKU-USB-001'],
            ['name' => 'USB', 'quantity' => 200],
        );
    }
}
