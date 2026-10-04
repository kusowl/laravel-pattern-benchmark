<?php

namespace App\Http\Controllers\Benchmark;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\HandlesCheckout;
use Illuminate\Http\JsonResponse;

/**
 * Benchmark — Trait Pattern
 *
 * Every method uses only traits — no actions, no services.
 * HandlesCheckout already composes CalculatesTax + CalculatesLoyalty,
 * so a single `use` brings calculateTax() and calculateLoyalty() into scope.
 * All config/dep resolution via app() helper inside the traits (no constructor DI).
 *
 * Trait graph:
 *   use HandlesCheckout
 *         ├── use CalculatesTax     → calculateTax()
 *         └── use CalculatesLoyalty → calculateLoyalty()
 */
class CheckoutTraitController extends Controller
{
    // One trait pulls in the full stack: HandlesCheckout, CalculatesTax, CalculatesLoyalty.
    use HandlesCheckout;

    /** Full checkout: tax + loyalty + status update. */
    public function checkout(int $orderId): JsonResponse
    {
        $order     = Order::findOrFail($orderId);
        $processed = $this->processCheckout($order);

        return response()->json([
            'pattern'        => 'trait',
            'route'          => 'checkout',
            'order_id'       => $processed->id,
            'subtotal'       => $processed->subtotal,
            'tax_amount'     => $processed->tax_amount,
            'total'          => $processed->total,
            'loyalty_points' => $processed->loyalty_points,
            'status'         => $processed->status,
        ]);
    }

    /** Tax info: compute tax from the order's current subtotal. */
    public function tax(int $orderId): JsonResponse
    {
        $order     = Order::findOrFail($orderId);
        $taxAmount = $this->calculateTax($order->subtotal);

        return response()->json([
            'pattern'    => 'trait',
            'route'      => 'tax',
            'order_id'   => $order->id,
            'subtotal'   => $order->subtotal,
            'tax_rate'   => ($this->taxRate * 100) . '%',
            'tax_amount' => $taxAmount,
        ]);
    }

    /** Loyalty info: compute loyalty points from the order's current total. */
    public function loyalty(int $orderId): JsonResponse
    {
        $order         = Order::findOrFail($orderId);
        $loyaltyPoints = $this->calculateLoyalty($order->total ?: $order->subtotal);

        return response()->json([
            'pattern'           => 'trait',
            'route'             => 'loyalty',
            'order_id'          => $order->id,
            'based_on_total'    => $order->total ?: $order->subtotal,
            'points_per_dollar' => $this->loyaltyPointsPerDollar,
            'loyalty_points'    => $loyaltyPoints,
        ]);
    }
}
