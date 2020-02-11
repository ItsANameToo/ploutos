<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    protected $casts = ['transaction' => 'array', 'transaction_id' => 'string'];

    /**
     * Set custom primary key.
     */
    public $primaryKey = 'transaction_id';
    public $incrementing = false;

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
