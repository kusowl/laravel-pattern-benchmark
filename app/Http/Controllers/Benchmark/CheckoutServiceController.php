<?php

namespace App\Http\Controllers\Benchmark;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\LoyaltyService;
use App\Services\TaxService;
use Illuminate\Http\JsonResponse;

/**
 * Benchmark — Service Pattern
 *
 * Every method uses only services — no actions, no traits.
 * All dependencies are injected via constructor DI.
 *
 * Dependency graph:
 *   checkout() → CheckoutService
 *                  ├── TaxService     (DI into service)
 *                  └── LoyaltyService (DI into service)
 *   tax()      → TaxService     (direct)
 *   loyalty()  → LoyaltyService (direct)
 */
class CheckoutServiceController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private readonly TaxService      $taxService,
        private readonly LoyaltyService  $loyaltyService,
    ) {}

    /** Full checkout: tax + loyalty + status update. */
    public function checkout(int $orderId): JsonResponse
    {
        $order     = Order::findOrFail($orderId);
        $processed = $this->checkoutService->process($order);

        return response()->json([
            'pattern'        => 'service',
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
        $taxAmount = $this->taxService->calculate($order->subtotal);

        return response()->json([
            'pattern'    => 'service',
            'route'      => 'tax',
            'order_id'   => $order->id,
            'subtotal'   => $order->subtotal,
            'tax_rate'   => ($this->taxService->rate() * 100) . '%',
            'tax_amount' => $taxAmount,
        ]);
    }

    /** Loyalty info: compute loyalty points from the order's current total. */
    public function loyalty(int $orderId): JsonResponse
    {
        $order         = Order::findOrFail($orderId);
        $loyaltyPoints = $this->loyaltyService->calculate($order->total ?: $order->subtotal);

        return response()->json([
            'pattern'           => 'service',
            'route'             => 'loyalty',
            'order_id'          => $order->id,
            'based_on_total'    => $order->total ?: $order->subtotal,
            'points_per_dollar' => $this->loyaltyService->pointsPerDollar(),
            'loyalty_points'    => $loyaltyPoints,
        ]);
    }
}
