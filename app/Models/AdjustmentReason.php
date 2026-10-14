<?php

namespace App\Models;

use App\Models\Concerns\HasIsDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 批次更改原因
 */
class AdjustmentReason extends Model
{
    use HasIsDelete;

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'integer',
            'is_delete' => 'integer',
        ];
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class, 'reason_id');
    }
}
