<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['transaction_id', 'transaction'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['transaction' => 'array'];

    /**
     * A transaction can have multiple disbursements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disbursement(): BelongsTo
    {
        return $this->hasMany(Disbursement::class);
    }

    /**
     * Get a transaction by the given transaction id.
     *
     * @return \App\Models\Transaction
     */
    public static function findByTransactionId(string $value): self
    {
        return static::whereTransactionId($value)->firstOrFail();
    }
}
