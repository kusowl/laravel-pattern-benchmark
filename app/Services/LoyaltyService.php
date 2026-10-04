<?php

namespace App\Services;

class LoyaltyService
{
    /**
     * Points earned per dollar spent (after tax).
     */
    private int $pointsPerDollar = 10;

    /**
     * Calculate loyalty points for a given total.
     * No real DB state — pure computation.
     */
    public function calculate(float $total): int
    {
        return (int) floor($total * $this->pointsPerDollar);
    }

    public function pointsPerDollar(): int
    {
        return $this->pointsPerDollar;
    }
}
