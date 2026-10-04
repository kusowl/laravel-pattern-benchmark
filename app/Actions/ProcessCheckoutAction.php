<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Action Pattern — Orchestrator
 *
 * Composes CalculateTaxAction and CalculateLoyaltyAction via constructor DI.
 * No services, no traits — pure action-to-action composition.
 *
 * Dependency graph (all actions):
 *   ProcessCheckoutAction
 *     ├── CalculateTaxAction     (injected)
 *     ├── CalculateLoyaltyAction (injected)
 *     └── Mailer                 (injected — Laravel's own contract)
 */
class ProcessCheckoutAction
{
    public function __construct(
        private readonly CalculateTaxAction     $calculateTax,
        private readonly CalculateLoyaltyAction $calculateLoyalty,
        private readonly Mailer                 $mailer,   // ← Laravel's own contract
    ) {}

    public function __invoke(Order $order): Order
    {
        $tax           = ($this->calculateTax)($order->subtotal);
        $total         = round($order->subtotal + $tax, 2);
        $loyaltyPoints = ($this->calculateLoyalty)($total);

        $order->update([
            'tax_amount'     => $tax,
            'total'          => $total,
            'loyalty_points' => $loyaltyPoints,
            'status'         => 'processed',
        ]);

        // Simulate mail — Mailer resolved from the container; 'log' / 'array'
        // driver in .env means nothing is actually sent.
        $this->mailer->raw(
            "[Action] Order #{$order->id} confirmed. Total: \${$total}",
            fn ($msg) => $msg->to('customer@example.com')->subject('Order Confirmation'),
        );

        return $order->fresh();
    }
}
