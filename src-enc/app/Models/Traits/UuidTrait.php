<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait UuidTrait
{
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = $model->uuid == null ? Str::uuid() : $model->uuid;
        });
    }

    public function scopeWhereUuidIn($query, array $uuids)
    {
        return $query->whereIn('uuid', $uuids);
    }
}
