<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInventoryAdjustmentRequest;
use App\Http\Resources\InventoryAdjustmentResource;
use App\Http\Responses\ApiResponse;
use App\Models\Batch;
use App\Models\InventoryAdjustment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InventoryAdjustmentController extends Controller
{
    /**
     * 调整库存
     *
     * @param StoreInventoryAdjustmentRequest $request
     * @return JsonResponse
     */
    public function store(StoreInventoryAdjustmentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        /**
         * 调整库存事务
         */
        $adjustment = DB::transaction(function () use ($validated) {
            $batch = Batch::query()->lockForUpdate()->findOrFail($validated['batch_id']);

            $oldQuantity = $batch->quantity;
            $newQuantity = $validated['new_quantity'];

            $adjustment = InventoryAdjustment::create([
                'batch_id' => $batch->id,
                'reason_id' => $validated['reason_id'],
                'old_quantity' => $oldQuantity,
                'new_quantity' => $newQuantity,
                'quantity_difference' => $newQuantity - $oldQuantity,
                'note' => $validated['note'] ?? '',
            ]);

            $batch->update(['quantity' => $newQuantity]);

            return $adjustment;
        });

        return (new InventoryAdjustmentResource($adjustment))
            ->additional(ApiResponse::successMeta())
            ->response();
    }

    /**
     * 根据ID获取单个调整记录
     */
    public function show(InventoryAdjustment $inventoryAdjustment): InventoryAdjustmentResource
    {
        $inventoryAdjustment->load(['batch.product', 'batch.warehouse', 'reason']);

        return (new InventoryAdjustmentResource($inventoryAdjustment))
            ->additional(ApiResponse::successMeta());
    }
}
