<?php

namespace App\Traits;

/**
 * Trait Pattern — Step 2
 *
 * Provides loyalty point calculation directly on the using class.
 * No services, no actions — pure trait method.
 * Used by HandlesCheckout trait.
 */
trait CalculatesLoyalty
{
    private int $loyaltyPointsPerDollar = 10;

    protected function calculateLoyalty(float $total): int
    {
        return (int) floor($total * $this->loyaltyPointsPerDollar);
    }
}
