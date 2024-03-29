<?php

namespace App\Models;

use App\Models\Concerns\CanBeBanned;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    use CanBeBanned;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['address', 'public_key', 'balance', 'earnings', 'payout_perc', 'payout_address'];

    /**
     * A wallet owns many disbursements.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function disbursements(): HasMany
    {
        return $this->hasMany(Disbursement::class);
    }

    /**
     * Get a wallet by the given address.
     *
     * @return \App\Models\Wallet
     */
    public static function findByAddress(string $value): self
    {
        return static::whereAddress($value)->firstOrFail();
    }

    /**
     * Get the latest disbursement of the wallet.
     *
     * @return \App\Models\Disbursement
     */
    public function latestDisbursement(): Disbursement
    {
        return $this->disbursements()->latest()->first();
    }

    /**
     * Get the total balance of the wallet. Sum of balance and earnings.
     *
     * @return int
     */
    public function getStakeAttribute(): int
    {
        return config('delegate.staking')
            ? $this->balance + $this->earnings
            : $this->balance;
    }

    /**
     * Scope a query to only include public wallets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublic(Builder $query): Builder
    {
        if (config('delegate.forceWhitelist')) {
            return $query->whitelisted();
        }

        return $query->notBanned()->notBlacklisted();
    }

    /**
     * Scope a query to only include non-blacklisted wallets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotBlacklisted(Builder $query): Builder
    {
        return $query->whereNotIn('address', config('delegate.blacklist'));
    }

    /**
     * Scope a query to only include whitelisted wallets.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhitelisted(Builder $query): Builder
    {
        return $query->whereIn('address', config('delegate.whitelist'));
    }

    /**
     * Determine if the wallet should be paid.
     */
    public function shouldBePaid(): bool
    {
        if (in_array($this->address, config('delegate.blacklist'), true)) {
            return false;
        }

        if ($this->banned_at) {
            return false;
        }

        $earnings = $this->earnings;

        if (!config('delegate.fees.cover')) {
            $earnings -= 0.1 * ARKTOSHI;
        }

        return $earnings >= (config('delegate.threshold') * ARKTOSHI);
    }
}
