<?php

namespace App\Models;

use App\Models\Concerns\HasIsDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryAdjustment extends Model
{
    use HasIsDelete;

    protected $fillable = [
        'batch_id',
        'reason_id',
        'old_quantity',
        'new_quantity',
        'quantity_difference',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'is_delete' => 'integer',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(AdjustmentReason::class);
    }
}
