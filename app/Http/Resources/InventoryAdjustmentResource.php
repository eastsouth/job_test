<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryAdjustmentResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'batch_id' => $this->batch_id,
            'reason_id' => $this->reason_id,
            'old_quantity' => $this->old_quantity,
            'new_quantity' => $this->new_quantity,
            'quantity_difference' => $this->quantity_difference,
            'note' => $this->note,
            'created_at' => $this->created_at,
            'batch' => $this->whenLoaded('batch', function () {
                return [
                    'id' => $this->batch->id,
                    'code' => $this->batch->code,
                    'quantity' => $this->batch->quantity,
                    'product' => [
                        'id' => $this->batch->product->id,
                        'name' => $this->batch->product->name,
                        'sku' => $this->batch->product->sku,
                    ],
                    'warehouse' => [
                        'id' => $this->batch->warehouse->id,
                        'name' => $this->batch->warehouse->name,
                        'location' => $this->batch->warehouse->location,
                    ],
                ];
            }),
            'reason' => new AdjustmentReasonResource($this->whenLoaded('reason')),
        ];
    }
}
