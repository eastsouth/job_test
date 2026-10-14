<?php

namespace App\Models;

use App\Models\Concerns\HasIsDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasIsDelete;

    protected $fillable = [
        'code',
        'warehouse_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'is_delete' => 'integer',
        ];
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function inventoryAdjustments(): HasMany
    {
        return $this->hasMany(InventoryAdjustment::class);
    }
}
