<?php

namespace App\Http\Controllers\Benchmark;

use App\Actions\CalculateLoyaltyAction;
use App\Actions\CalculateTaxAction;
use App\Actions\ProcessCheckoutAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

/**
 * Benchmark — Action Pattern
 *
 * Every method uses only actions — no services, no traits.
 * All dependencies are injected via constructor DI.
 *
 * Dependency graph:
 *   checkout() → ProcessCheckoutAction
 *                  ├── CalculateTaxAction     (DI into action)
 *                  └── CalculateLoyaltyAction (DI into action)
 *   tax()      → CalculateTaxAction     (direct)
 *   loyalty()  → CalculateLoyaltyAction (direct)
 */
class CheckoutActionController extends Controller
{
    /** Full checkout: tax + loyalty + status update. */
    public function checkout(int $orderId, ProcessCheckoutAction $processCheckoutAction): JsonResponse
    {
        $order     = Order::findOrFail($orderId);
        $processed = ($processCheckoutAction)($order);

        return response()->json([
            'pattern'        => 'action',
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
    public function tax(int $orderId, CalculateTaxAction $calculateTaxAction): JsonResponse
    {
        $order      = Order::findOrFail($orderId);
        $taxAmount  = ($calculateTaxAction)($order->subtotal);

        return response()->json([
            'pattern'    => 'action',
            'route'      => 'tax',
            'order_id'   => $order->id,
            'subtotal'   => $order->subtotal,
            'tax_rate'   => '18%',
            'tax_amount' => $taxAmount,
        ]);
    }

    /** Loyalty info: compute loyalty points from the order's current total. */
    public function loyalty(int $orderId, CalculateLoyaltyAction $calculateLoyaltyAction): JsonResponse
    {
        $order         = Order::findOrFail($orderId);
        $loyaltyPoints = ($calculateLoyaltyAction)($order->total ?: $order->subtotal);

        return response()->json([
            'pattern'          => 'action',
            'route'            => 'loyalty',
            'order_id'         => $order->id,
            'based_on_total'   => $order->total ?: $order->subtotal,
            'points_per_dollar'=> 10,
            'loyalty_points'   => $loyaltyPoints,
        ]);
    }
}
