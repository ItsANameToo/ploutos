<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Block extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['block_id', 'height', 'reward', 'fee', 'forged_at', 'processed_at'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['forged_at', 'processed_at'];

    /**
     * Scope a query to only include processed blocks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeProcessed(Builder $query): Builder
    {
        return $query->whereNotNull('processed_at');
    }

    /**
     * Scope a query to only include unprocessed blocks.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotProcessed(Builder $query): Builder
    {
        return $query->whereNull('processed_at');
    }

    /**
     * Mark the block as processed.
     *
     * @return bool
     */
    public function markAsProcessed(): bool
    {
        return $this->update(['processed_at' => Carbon::now()]);
    }

    /**
     * Mark the block as not processed.
     *
     * @return bool
     */
    public function markAsNotProcessed(): bool
    {
        return $this->update(['processed_at' => null]);
    }

    /**
     * Get the total value we will use for calculations.
     *
     * @return int
     */
    public function getTotalAttribute(): int
    {
        return config('delegate.fees.share')
            ? $this->reward + $this->fee
            : $this->reward;
    }
}
