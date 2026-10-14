<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdjustmentReasonResource;
use App\Http\Responses\ApiResponse;
use App\Models\AdjustmentReason;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdjustmentReasonController extends Controller
{
    /**
     * 列出修改库存可选原因
     *
     * @return AnonymousResourceCollection
     */
    public function list(): AnonymousResourceCollection
    {
        $reasons = AdjustmentReason::query()
            ->where('is_active', 1)
            ->orderBy('id')
            ->get();

        return AdjustmentReasonResource::collection($reasons)
            ->additional(ApiResponse::successMeta());
    }
}
