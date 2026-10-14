<?php

namespace App\Models;

use App\Models\Concerns\HasIsDelete;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasIsDelete;

    protected $fillable = [
        'name',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'is_delete' => 'integer',
        ];
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
}
