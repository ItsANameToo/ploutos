<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Disbursement extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['transaction_id', 'amount', 'purpose', 'signed_at', 'transaction'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['signed_at'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['transaction' => 'array'];

    /**
     * A disbursement is owned by a wallet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get a disbursement by the given transaction id.
     *
     * @return \App\Models\Disbursement
     */
    public static function findByTransactionId(string $value): self
    {
        return static::whereTransactionId($value)->firstOrFail();
    }

    /**
     * Scope a query to only include payouts since last midnight.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSinceMidnight($query)
    {
        return $query->where('signed_at', '>=', now()->startOfDay());
    }
}
