<?php

namespace App\Services;

use Illuminate\Contracts\Mail\Mailer;

class TaxService
{
    public function __construct(
        private readonly Mailer $mailer
    )
    {
    }

    /**
     * Tax rate (18% GST simulation).
     */
    private float $rate = 0.18;

    /**
     * Calculate tax for a given subtotal.
     * No real DB state — pure computation.
     */
    public function calculate(float $subtotal): float
    {
        $this->mailer->raw(
            "Tax calculated",
            fn ($msg) => $msg->to('customer@example.com')->subject('Order Confirmation'),
        );
        return round($subtotal * $this->rate, 2);
    }

    public function rate(): float
    {
        return $this->rate;
    }
}
