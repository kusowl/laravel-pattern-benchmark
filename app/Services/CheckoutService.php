<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Contracts\Mail\Mailer;

class CheckoutService
{
    /**
     * Service Pattern — Constructor DI
     *
     * Laravel resolves Illuminate\Contracts\Mail\Mailer from the container
     * (bound to Illuminate\Mail\Mailer by the MailServiceProvider).
     * No custom interface or binding needed.
     */
    public function __construct(
        private readonly TaxService     $taxService,
        private readonly LoyaltyService $loyaltyService,
        private readonly Mailer         $mailer,       // ← Laravel's own contract
    ) {}

    /**
     * Process checkout for an order:
     * 1. Calculate tax on subtotal.
     * 2. Compute total (subtotal + tax).
     * 3. Award loyalty points based on total.
     * 4. Update order status to "processed".
     * 5. Simulate mail dispatch via Mailer::raw().
     */
    public function process(Order $order): Order
    {
        $tax            = $this->taxService->calculate($order->subtotal);
        $total          = round($order->subtotal + $tax, 2);
        $loyaltyPoints  = $this->loyaltyService->calculate($total);

        $order->update([
            'tax_amount'     => $tax,
            'total'          => $total,
            'loyalty_points' => $loyaltyPoints,
            'status'         => 'processed',
        ]);

        // Simulate mail — Mailer resolved from the container; 'log' / 'array'
        // driver in .env means nothing is actually sent.
        $this->mailer->raw(
            "[Service] Order #{$order->id} confirmed. Total: \${$total}",
            fn ($msg) => $msg->to('customer@example.com')->subject('Order Confirmation'),
        );

        return $order->fresh();
    }
}
