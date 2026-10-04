<?php

namespace App\Traits;

use App\Models\Order;
use Illuminate\Contracts\Mail\Mailer;

/**
 * Trait Pattern — Orchestrator
 *
 * Composes CalculatesTax and CalculatesLoyalty traits via PHP trait use.
 * Dependencies (tax rate, loyalty rate) resolved inline — no services, no actions.
 *
 * Mailer note: traits cannot declare constructor parameters, so the container
 * is accessed via app() at call-time. The resolved instance is identical to
 * what constructor DI would provide — same binding, same lifecycle.
 *
 * Dependency graph (all traits):
 *   HandlesCheckout
 *     ├── use CalculatesTax       (calculateTax method resolved inline)
 *     ├── use CalculatesLoyalty   (calculateLoyalty method resolved inline)
 *     └── Mailer                  (app() pull — Laravel's own contract)
 */
trait HandlesCheckout
{
    use CalculatesTax;
    use CalculatesLoyalty;

    protected function processCheckout(Order $order): Order
    {
        $tax           = $this->calculateTax($order->subtotal);
        $total         = round($order->subtotal + $tax, 2);
        $loyaltyPoints = $this->calculateLoyalty($total);

        $order->update([
            'tax_amount'     => $tax,
            'total'          => $total,
            'loyalty_points' => $loyaltyPoints,
            'status'         => 'processed',
        ]);

        // Traits have no constructor, so resolve Mailer from the container
        // via app() — same instance the container would inject elsewhere.
        /** @var Mailer $mailer */
        $mailer = app(Mailer::class);

        $mailer->raw(
            "[Trait] Order #{$order->id} confirmed. Total: \${$total}",
            fn ($msg) => $msg->to('customer@example.com')->subject('Order Confirmation'),
        );

        return $order->fresh();
    }
}
