<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class BatchSeeder extends Seeder
{
    /**
     * 填充初始数据
     *
     * @return void
     */
    public function run(): void
    {
        $main = Warehouse::where('name', '一号仓库')->firstOrFail();
        $secondary = Warehouse::where('name', '二号仓库')->firstOrFail();

        $mouse = Product::where('sku', 'SKU-MOUSE-001')->firstOrFail();
        $keyboard = Product::where('sku', 'SKU-KBD-001')->firstOrFail();
        $usb = Product::where('sku', 'SKU-USB-001')->firstOrFail();

        Batch::updateOrCreate(
            ['code' => 'BATCH-001'],
            ['warehouse_id' => $main->id, 'product_id' => $mouse->id, 'quantity' => 100],
        );

        Batch::updateOrCreate(
            ['code' => 'BATCH-002'],
            ['warehouse_id' => $main->id, 'product_id' => $keyboard->id, 'quantity' => 50],
        );

        Batch::updateOrCreate(
            ['code' => 'BATCH-003'],
            ['warehouse_id' => $secondary->id, 'product_id' => $usb->id, 'quantity' => 200],
        );

        Batch::updateOrCreate(
            ['code' => 'BATCH-004'],
            ['warehouse_id' => $secondary->id, 'product_id' => $mouse->id, 'quantity' => 30],
        );
    }
}
