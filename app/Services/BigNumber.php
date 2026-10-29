<?php

namespace App\Services;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

class BigNumber
{
    /**
     * The underlying decimal value.
     *
     * @var \Brick\Math\BigDecimal
     */
    private $value;

    /**
     * Create a new instance.
     *
     * @param \Brick\Math\BigDecimal $value
     */
    private function __construct(BigDecimal $value)
    {
        $this->value = $value;
    }

    /**
     * Create a new instance.
     *
     * @param mixed $value
     *
     * @return \App\Services\BigNumber
     */
    public static function create($value): self
    {
        return new static(static::toDecimal($value));
    }

    /**
     * Multiply the current value by the given value.
     *
     * @param mixed $value
     *
     * @return \App\Services\BigNumber
     */
    public function times($value): self
    {
        return new static($this->value->multipliedBy(static::toDecimal($value)));
    }

    /**
     * Divide the current value by the given value.
     *
     * @param mixed $value
     * @param int   $scale
     *
     * @return \App\Services\BigNumber
     */
    public function dividedBy($value, int $scale = 0): self
    {
        return new static($this->value->dividedBy(static::toDecimal($value), $scale, RoundingMode::HalfDown));
    }

    /**
     * Get an integer representation of the object.
     *
     * @return int
     */
    public function toInteger(): int
    {
        return $this->value->toInt();
    }

    /**
     * Get a string representation of the object.
     *
     * @return string
     */
    public function toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Convert the given value to a decimal.
     *
     * @param mixed $value
     *
     * @return \Brick\Math\BigDecimal
     */
    private static function toDecimal($value): BigDecimal
    {
        if ($value instanceof self) {
            return $value->value;
        }

        return BigDecimal::of((string) $value);
    }
}
