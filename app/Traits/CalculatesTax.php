<?php

namespace App\Traits;

use Illuminate\Contracts\Mail\Mailer;

/**
 * Trait Pattern — Step 1
 *
 * Provides tax calculation directly on the using class.
 * No services, no actions — pure trait method.
 * Used by HandlesCheckout trait.
 */
trait CalculatesTax
{
    private float $taxRate = 0.18;

    protected function calculateTax(float $subtotal): float
    {
        // Traits have no constructor, so resolve Mailer from the container
        // via app() — same instance the container would inject elsewhere.
        /** @var Mailer $mailer */
        $mailer = app(Mailer::class);

        $mailer->raw(
            "Tax calculated",
            fn ($msg) => $msg->to('customer@example.com')->subject('Order Confirmation'),
        );
        return round($subtotal * $this->taxRate, 2);
    }
}
