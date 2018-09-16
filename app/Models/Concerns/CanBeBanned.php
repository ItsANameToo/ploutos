<?php

namespace App\Models\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait CanBeBanned
{
    /**
     * Ban the entity.
     *
     * @return bool
     */
    public function ban(): bool
    {
        return $this->forceFill(['banned_at' => Carbon::now()])->save();
    }

    /**
     * Unban the entity.
     *
     * @return bool
     */
    public function unban(): bool
    {
        return $this->forceFill(['banned_at' => null])->save();
    }

    /**
     * Check if the entity is banned.
     *
     * @return bool
     */
    public function getIsBannedAttribute(): bool
    {
        return !empty($this->banned_at);
    }

    /**
     * Scope a query to only include banned entities.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBanned(Builder $query): Builder
    {
        return $query->whereNotNull('banned_at');
    }

    /**
     * Scope a query to only include not banned entities.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotBanned(Builder $query): Builder
    {
        return $query->whereNull('banned_at');
    }
}
