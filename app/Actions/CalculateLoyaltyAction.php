<?php

namespace App\Actions;

/**
 * Action Pattern — Step 2
 *
 * Single-responsibility action: computes loyalty points (10 pts/dollar of total).
 * Pure computation — no DB state, no services.
 * Injected via constructor DI into ProcessCheckoutAction.
 */
class CalculateLoyaltyAction
{
    private int $pointsPerDollar = 10;

    public function __invoke(float $total): int
    {
        return (int) floor($total * $this->pointsPerDollar);
    }
}
