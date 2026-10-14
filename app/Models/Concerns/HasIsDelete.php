<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * 软删除
 */
trait HasIsDelete
{
    /**
     * 默认查找未软删除的数据
     */
    public static function bootHasIsDelete(): void
    {
        static::addGlobalScope('is_delete', function (Builder $builder) {
            $builder->where('is_delete', 0);
        });
    }

    /**
     * 对数据进行软删除
     */
    public function softDelete(): bool
    {
        return $this->forceFill(['is_delete' => 1])->save();
    }

    /**
     * 对软删除数据进行恢复
     */
    public function restore(): bool
    {
        return $this->forceFill(['is_delete' => 0])->save();
    }
}
