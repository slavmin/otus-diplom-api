<?php

declare(strict_types=1);

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(fn (Model $model): string => $model->attributes['uuid'] = Str::uuid()->toString());
    }

    public static function findByUuid(string $uuid): ?Model
    {
        return static::query()->where('uuid', $uuid)->first();
    }
}
